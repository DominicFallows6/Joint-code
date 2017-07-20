<?php

namespace Limitless\Elastic\Adapter;

use Limitless\Elastic\Adapter\QueryBuilder as LimitlessQueryBuilder;
use Magento\Elasticsearch\Model\Config;
use Magento\Elasticsearch\SearchAdapter\Adapter as MagentoElasticAdapter;
use Magento\Elasticsearch\SearchAdapter\ConnectionManager;
use Magento\Elasticsearch\SearchAdapter\Mapper;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Elasticsearch\SearchAdapter\ResponseFactory;
use Magento\Elasticsearch\SearchAdapter\Aggregation\Builder as AggregationBuilder;

class SearchAdapter implements AdapterInterface
{
    private $query;

    /**
     * @var MagentoElasticAdapter
     */
    private $magentoElasticAdapter;

    /** @var ResponseFactory $responseFactory */
    private $responseFactory;

    /** @var Config  */
    private $config;

    /**
     * @var \Limitless\Elastic\Adapter\QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    public function __construct(
        MagentoElasticAdapter $magentoElasticAdapter,
        ResponseFactory $responseFactory,
        LimitlessQueryBuilder $queryBuilder,
        ConnectionManager $connectionManager,
        Mapper $mapper,
        AggregationBuilder $aggregationBuilder,
        \Magento\Framework\App\RequestInterface $request,
        Config $config,
        \Magento\Directory\Model\Currency $currency
    ) {
        $this->responseFactory = $responseFactory;
        $this->magentoElasticAdapter = $magentoElasticAdapter;
        $this->queryBuilder = $queryBuilder;
        $this->connectionManager = $connectionManager;
        $this->mapper = $mapper;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Process Search Request
     *
     * @param RequestInterface $request
     * @return QueryResponse
     */
    public function query(RequestInterface $request)
    {
        $requestName = $request->getName();
        $this->query = $this->mapper->buildQuery($request);
        $this->configureQueryBuilder();

        if ($requestName === 'quick_search_container') {

            $results = $this->queryBuilder->createRequest(
                $this->request->getParams(),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                'q',
                ['_id', '_score']
            );

            return $this->finaliseResults($request, $results);

        } elseif ($requestName === 'catalog_view_container') {

            $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];

            $results = $this->queryBuilder->createCategoryRequest(
                $this->request->getParams(),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                $categoryId,
                ['_id', '_score']
            );

            return $this->finaliseResults($request, $results);

        }

        return $this->magentoElasticAdapter->query($request);

    }

    private function finaliseResults(RequestInterface $request, QueryResultsInterface $results):QueryResponse
    {
        $hits = [];
        $hits['hits'] = $results->getResults();
        $hits['total'] = count($hits['hits']);

        $rawResponse = [
            'timed_out' => false,
            'aggregations' => $results->getFilters(),
            'hits' => $hits
        ];

        //runs the same query without the price filters - only way to maintain the facets across searches that have multiple attributes applied
        //should be able to modify this to call in one query
        //need to do this so that the filters remain the same across multiple requests
        //magento tries to make changes as price is a dynamic bucket
        $getWithPriceParams = $this->request->getParams();

        $prePriceAggregations = [];

        if (isset($getWithPriceParams['price'])) {

            $requestName = $request->getName();
            $pricePreferenceParameters = $this->request->getParams();
            unset($pricePreferenceParameters['price']);

            if ($requestName === 'quick_search_container') {

                $getWithPriceParams = ['q'=>$getWithPriceParams['q']];

                $withoutPrices = $this->queryBuilder->createRequest(
                    $getWithPriceParams,
                    $this->createRequestAggregationsArray(),
                    $this->createQueryFieldsArray(),
                    'q',
                    ['_id', '_score']
                );

            } elseif($requestName === 'catalog_view_container') {

                $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];

                $withoutPrices = $this->queryBuilder->createCategoryRequest(
                    $pricePreferenceParameters,
                    $this->createRequestAggregationsArray(),
                    $this->createQueryFieldsArray(),
                    $categoryId,
                    ['_id', '_score']
                );

            }

            $prePriceAggregationsResponse = $rawResponse;
            $prePriceAggregationsResponse['aggregations'] = $withoutPrices->getFilters();
            $prePriceAggregationsResponse['hits']['hits'] = $withoutPrices->getResults();
            $prePriceAggregationsResponse['hits']['total'] = count($prePriceAggregationsResponse['hits']['total']);

            $prePriceAggregations = $this->aggregationBuilder->build($request, $prePriceAggregationsResponse);

            //we then need to ensure that once we have the same aggregations
            //we need to apply the filters to get the correct numbers - these are then "mapped" below
            if (!$this->priceOnly()) {

                if ($requestName === 'quick_search_container') {

                    $preferencePrices = $this->queryBuilder->createRequest(
                        $pricePreferenceParameters,
                        $this->createRequestAggregationsArray(),
                        $this->createQueryFieldsArray(),
                        'q',
                        ['_id', '_score'],
                        true
                    );

                } elseif ($requestName === 'catalog_view_container') {

                    $preferencePrices = $this->queryBuilder->createCategoryRequest(
                        $pricePreferenceParameters,
                        $this->createRequestAggregationsArray(),
                        $this->createQueryFieldsArray(),
                        $categoryId,
                        ['_id', '_score'],
                        true
                    );

                }

                //copy original values
                $pricePreferencesResponse = $rawResponse;

                $pricePreferencesResponse['aggregations'] = $preferencePrices->getFilters();
                $pricePreferencesResponse['hits']['hits'] = $preferencePrices->getResults();
                $pricePreferencesResponse['hits']['total'] = count($pricePreferencesResponse['hits']['hits']);

                $pricePreferenceAggregations = $this->aggregationBuilder->build($request, $pricePreferencesResponse);

                //map true counts over the top and remove the un-required ones
                foreach($prePriceAggregations['price_bucket'] as $priceMappedKey => $priceMappedValue) {
                    if (isset($pricePreferenceAggregations['price_bucket'][$priceMappedKey])) {
                        $prePriceAggregations['price_bucket'][$priceMappedKey]['count'] = $pricePreferenceAggregations['price_bucket'][$priceMappedKey]['count'];
                    } else {
                        unset($prePriceAggregations['price_bucket'][$priceMappedKey]);
                    }
                }

            }

        }

        //skips to here when search is "basic"
        $completeAggregations = $this->aggregationBuilder->build($request, $rawResponse);

        if (!empty($prePriceAggregations) && isset($completeAggregations['price_bucket'])) {

            $completeAggregations['price_bucket'] = $prePriceAggregations['price_bucket'];
            $currentlyUsedParams = $this->request->getParams();

            if ($requestName === 'quick_search_container') {

                $currentlyUsedParamsCount = count($currentlyUsedParams['price']);
                $numberOfPriceBuckets = count($completeAggregations['price_bucket']);

                if ($currentlyUsedParamsCount >= $numberOfPriceBuckets) {
                    $completeAggregations['price_bucket'] = [];
                }

            } elseif ($requestName === 'catalog_view_container') {

                foreach ($currentlyUsedParams['price'] as $currentKey => $currentValue) {

                    $modifiedElementName = str_replace('-', '_', $currentValue);

                    if (isset($completeAggregations['price_bucket'][$modifiedElementName])) {
                        $elementToBeRemoved = $modifiedElementName;
                    } else {

                        $explodedValues = explode('-', $currentValue);

                        if ($explodedValues[0] == '') {
                            $elementToBeRemoved = '*_' . $explodedValues[1];
                        } elseif ($explodedValues[1] == '') {
                            $elementToBeRemoved = $explodedValues[0] . '_*';
                        }

                    }

                    if (isset($elementToBeRemoved)) {
                        if (isset($completeAggregations['price_bucket'][$elementToBeRemoved])) {
                            unset($completeAggregations['price_bucket'][$elementToBeRemoved]);
                        }
                    }


                    //todo when we do the price filter work
                    //add an arbitrary filter in. think magento does some sort of count in category view page -
                    if (isset($completeAggregations['price_bucket']) && count($completeAggregations['price_bucket']) == 1) {
                        $completeAggregations['price_bucket']['0'] = ['value'=>'0', 'count'=>0];
                    }

                }
            }

        }

        $response = $this->responseFactory->create([
            'documents' => $hits['hits'],
            'aggregations' => $completeAggregations,
        ]);

        return $response;
    }

    private function createRequestAggregationsArray(): array
    {
        $requestAggregations = $this->query['body']['aggregations'];
        return $requestAggregations;
    }

    private function createQueryFieldsArray():array
    {
        $queryFields = [];
        if (isset($this->query['body']['query']['bool']['should'])) {
            foreach ($this->query['body']['query']['bool']['should'] as $queryField) {

                if (isset($queryField['match'])) {
                    $key = key($queryField['match']);
                } elseif (isset($queryField['terms'])) {
                    $key = key($queryField['terms']);
                }

                if ($key !== '_all') {
                    $queryFields[] = $key;
                }
            }
        }
        return $queryFields;
    }

    //this takes the core mapped keys and applies the URL values to our internal mapping
    private function initialiseQueryBuilderMapOfQueryFieldsToInternalNames()
    {
        $fieldsThatNeedMapping = ['price'];
        $getMappedAggregations = $this->query['body']['aggregations'];
        $mappings = [];

        foreach ($fieldsThatNeedMapping as $needMapKey => $needMapValue) {
            $keyToFind = $needMapValue.'_bucket';
            if (isset($getMappedAggregations[$keyToFind])) {
                if (isset($getMappedAggregations[$keyToFind]['extended_stats']['field'])) {
                    $mappedKey = $getMappedAggregations[$keyToFind]['extended_stats']['field'];
                    $mappings[$needMapValue] = $mappedKey;
                }
            }
        }

        //other static mapped fields
        $mappings['cat'] = 'category_ids';

        $this->queryBuilder->setMappedQueryFields($mappings);
    }

    private function configureQueryBuilder()
    {
        $this->initialiseQueryBuilderMapOfQueryFieldsToInternalNames();

        //username and password available in this set of options too
        $options = $this->config->prepareClientOptions();
        $url = 'http://' . $options['hostname'] . ':' . $options['port'];

        //this will need to taken as a value from config
        $this->queryBuilder->setIndex($this->query['index']);
        $this->queryBuilder->setURL($url);
        $this->queryBuilder->setMethod('_search');
    }

    private function priceOnly() : bool
    {
        $params = $this->request->getParams();

        if (isset($params['q'])) {
            unset($params['q']);
        }

        if (isset($params['price'])) {
            unset($params['price']);
        }

        if (count($params) === 0) {
            return true;
        } else {
            return false;
        }
    }
}