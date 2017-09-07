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
use Limitless\Elastic\Helpers\ArrayHelper;

class SearchAdapter implements AdapterInterface
{
    private $query;

    /**
     * @var MagentoElasticAdapter
     */
    private $magentoElasticAdapter;

    /** @var ResponseFactory $responseFactory */
    private $responseFactory;

    /** @var Config */
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
        Config $config
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
                ArrayHelper::ensureMagento2Based2DArray($this->request->getParams()),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                'q',
                ['_id', '_score']
            );

            return $this->finaliseResults($request, $results);

        } elseif ($requestName === 'catalog_view_container') {

            $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];

            $results = $this->queryBuilder->createCategoryRequest(
                ArrayHelper::ensureMagento2Based2DArray($this->request->getParams()),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                $categoryId,
                ['_id', '_score']
            );

            return $this->finaliseResults($request, $results);

        }

        return $this->magentoElasticAdapter->query($request);
    }

    private function finaliseResults(RequestInterface $request, QueryResultsInterface $results): QueryResponse
    {
        $hits = [];
        $hits['hits'] = $results->getResults();
        $hits['total'] = count($hits['hits']);
        $requestName = $request->getName();

        $rawResponse = [
            'timed_out' => false,
            'aggregations' => $results->getFilters(),
            'hits' => $hits
        ];

        if ($requestName === 'quick_search_container') {

            $allParamSearch = $this->queryBuilder->createRequest(
                ArrayHelper::ensureMagento2Based2DArray($this->request->getParams()),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                'q',
                ['_id', '_score']
            );

        } elseif ($requestName === 'catalog_view_container') {

            $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];

            $allParamSearch = $this->queryBuilder->createCategoryRequest(
                ArrayHelper::ensureMagento2Based2DArray($this->request->getParams()),
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                $categoryId,
                ['_id', '_score']
            );

        }

        $basePriceResponse = $rawResponse;
        $basePriceResponse['aggregations'] = $allParamSearch->getFilters();
        $basePriceResponse['hits']['hits'] = $allParamSearch->getResults();
        $basePriceResponse['hits']['total'] = count($basePriceResponse['hits']['total']);

        $baseSearchAggregations = $this->aggregationBuilder->build($request, $basePriceResponse);

        //Uses the dynamic results to create multi-faceted price results
        if (!empty($baseSearchAggregations['price_bucket'])) {
            $priceFacetAggregations = $this->createPriceFacets($request, $rawResponse);
            $baseSearchAggregations['price_bucket'] = $priceFacetAggregations;
        }

        $response = $this->responseFactory->create([
            'documents' => $hits['hits'],
            'aggregations' => $baseSearchAggregations,
        ]);

        return $response;
    }

    protected function createPriceFacets(RequestInterface $request, array $originalSearchResponse): array
    {
        $getAllURLParamsExceptPrice = $getAllURLParams = ArrayHelper::ensureMagento2Based2DArray($this->request->getParams());
        $requestName = $request->getName();
        $newlyFormedPriceAggregations = [];

        if (isset($getAllURLParamsExceptPrice['price'])) {
            unset($getAllURLParamsExceptPrice['price']);
        }

        if ($requestName === 'quick_search_container') {

            $baseSearchParameters = ['q' => $getAllURLParams['q']];

            $baseSearch = $this->queryBuilder->createRequest(
                $baseSearchParameters,
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                'q',
                ['_id', '_score']
            );

        } elseif ($requestName === 'catalog_view_container') {

            $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];

            $baseSearch = $this->queryBuilder->createCategoryRequest(
                [],
                $this->createRequestAggregationsArray(),
                $this->createQueryFieldsArray(),
                $categoryId,
                ['_id', '_score']
            );

        }

        $basePriceResponse = $originalSearchResponse;
        $basePriceResponse['aggregations'] = $baseSearch->getFilters();
        $basePriceResponse['hits']['hits'] = $baseSearch->getResults();
        $basePriceResponse['hits']['total'] = count($basePriceResponse['hits']['total']);

        $newSearchAggregations = $this->aggregationBuilder->build($request, $basePriceResponse);

        //reset the price bucket aggregations
        $priceBucketRequiredComparisons = $newSearchAggregations['price_bucket'];

        //remove ones already applied
        $priceParamsSorted = [];
        $priceParams = $this->request->getParam('price', []);

        if (!is_array($priceParams)) {
            $priceParams = (array) $priceParams;
        }

        if (!empty($priceParams)) {
            foreach ($priceParams as $priceKey => $priceValue) {
                $priceParamsSorted[] = $this->convertToAggregateGroupArrayKey($priceValue);
            }
        }

        foreach ($priceBucketRequiredComparisons as $priceComparisonBucketKey => $priceComparisonBucketValue) {

            if (!isset($priceParamsSorted[$priceComparisonBucketKey])) {

                $this->queryBuilder->enforceBroadSearchOption(false);
                $this->queryBuilder->setForceMustAggregationComparison(true);
                $this->queryBuilder->setForceMustNotAggregationComparison(false);

                /*
                 * this mocks the request parameters and supplies each price filter at a time
                 * need to do this as magento has to resolve the prices itself later along the request
                */
                $individualFiltersForPrice = $getAllURLParamsExceptPrice;
                $valueToPassAsParameter = $this->convertToUsableStringComparison($priceComparisonBucketValue['value']);
                $individualFiltersForPrice['price'][] = $valueToPassAsParameter;

                if ($requestName === 'quick_search_container') {
                    $individualPriceSearch = $this->queryBuilder->createRequest(
                        $individualFiltersForPrice,
                        $this->createRequestAggregationsArray(),
                        $this->createQueryFieldsArray(),
                        'q',
                        ['_id', '_score'],
                        true
                    );
                } elseif ($requestName === 'catalog_view_container') {
                    $categoryId = $this->query['body']['query']['bool']['must'][0]['term']['category_ids'];
                    $individualPriceSearch = $this->queryBuilder->createCategoryRequest(
                        $individualFiltersForPrice,
                        $this->createRequestAggregationsArray(),
                        $this->createQueryFieldsArray(),
                        $categoryId,
                        ['_id', '_score'],
                        true
                    );
                }

                $aggregations = $individualPriceSearch->getFilters();

                if (!in_array($valueToPassAsParameter, $priceParams)) {
                    $newlyFormedPriceAggregations[$priceComparisonBucketKey] = [
                        'value' => $priceComparisonBucketKey,
                        'count' => $aggregations['price_bucket']['count']
                    ];
                }

            }

        }

        //clean up for view bugs - arbitrary filter
        if (count($newlyFormedPriceAggregations) == 1) {
            $newlyFormedPriceAggregations['0'] = ['value' => '0', 'count' => 0];
        }

        return $newlyFormedPriceAggregations;
    }

    private function convertToAggregateGroupArrayKey(string $string, $delimiter = '-'): string
    {

        $returnString = '';
        $valuesAsArray = (explode($delimiter, $string));

        if ($valuesAsArray[0] == '') {
            $returnString .= '*';
        } else {
            $returnString .= $valuesAsArray[0];
        }

        $returnString .= '_';

        if ($valuesAsArray[1] == '') {
            $returnString .= '*';
        } else {
            $returnString .= $valuesAsArray[1];
        }

        return $returnString;
    }

    private function convertToUsableStringComparison(string $string): string
    {
        $modifiedString = str_replace('_', '-', (str_replace('*', '', $string)));
        return $modifiedString;
    }

    private function createRequestAggregationsArray(): array
    {
        $requestAggregations = $this->query['body']['aggregations'];
        return $requestAggregations;
    }

    private function createQueryFieldsArray(): array
    {
        $queryFields = [];
        if (isset($this->query['body']['query']['bool']['should'])) {
            foreach ($this->query['body']['query']['bool']['should'] as $queryField) {

                if (isset($queryField['match'])) {
                    $key = key($queryField['match']);
                } elseif (isset($queryField['terms'])) {
                    $key = key($queryField['terms']);
                } else {
                    $key = key($queryField['term']);
                }

                if ($key !== '_all') {
                    $queryFields[] = $key;
                }
            }
        }
        return $queryFields;
    }

    /*
     * This method takes the core mapped keys and applies the URL values to our internal mapping
     */
    private function initialiseQueryBuilderMapOfQueryFieldsToInternalNames()
    {
        $fieldsThatNeedMapping = ['price'];
        $getMappedAggregations = $this->query['body']['aggregations'];
        $mappings = [];

        foreach ($fieldsThatNeedMapping as $needMapKey => $needMapValue) {
            $keyToFind = $needMapValue . '_bucket';
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
}