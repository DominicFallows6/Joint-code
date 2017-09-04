<?php

namespace Limitless\Elastic\Adapter;

class QueryBuilder
{

    /**
     * @var string $keywordValue the keyword for non-category searches
     */
    private $keywordValue;

    /** @var string $keywordName special keyword element name in supplied search parameters */
    private $keywordName;

    /**
     * @var array $mappedFields Array of fields which are mapped by Magento e.g. price becomes price_0_2
     */
    private $mappedFields;

    /** @var string $url location of ES Cluster */
    private $url;

    /** @var string $index Name of the ES Index */
    private $index;

    /** @var string $elasticSearchRequestJSON ES Query String */
    private $elasticSearchRequestJSON;

    /**
     * @var array $responseData Associative Array of returned data from ES
     */
    private $responseData;

    /**
     * @var string $method Assigns the ES interaction method
     */
    private $method;

    /**
     * @var array $termFields 2D array of terms to filter against
     */
    private $termFields;

    /** @var array $filterAggregations 2D array of aggregation results returned from ES */
    private $filterAggregations;

    /**
     * @var array $filterHits 2D array for result hits
     */
    private $filterHits;

    /** @var bool $isBroadSearch Determines whether or not any filters are applied */
    private $isBroadSearch = false;

    /** @var array $aggregations Array containing the aggregations to be returned from the request */
    private $aggregations;

    /** @var string $queryType Contains the clause type for use in search */
    private $queryType = 'must';

    /** @var array $queryFields The fields to search against in ES */
    private $queryFields;

    /**
     * @var bool $forceMustNotAggregationComparison Forces a must not comparison for aggregations
     */
    private $forceMustNotAggregationComparison = false;

    /**
     * @var bool $forceMustNotAggregationComparison Forces a must comparison for aggregations
     */
    private $forceMustAggregationComparison = false;

    /**
     * @param bool $forceMustNotAggregationComparison
     */
    public function setForceMustNotAggregationComparison($forceMustNotAggregationComparison)
    {
        $this->forceMustNotAggregationComparison = $forceMustNotAggregationComparison;
    }

    /**
     * @param bool $forceMustAggregationComparison
     */
    public function setForceMustAggregationComparison($forceMustAggregationComparison)
    {
        $this->forceMustAggregationComparison = $forceMustAggregationComparison;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @param string $url
     */
    public function setURL($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function createRequest(
        array $searchInput,
        array $aggregations,
        array $queryFields,
        string $keywordName,
        array $returnFields = [],
        bool $enforceMustQueryType = false
    ): QueryResultsInterface {
        $terms = [];

        $this->keywordName = $keywordName;
        $this->keywordValue = $searchInput[$this->keywordName];
        $this->aggregations = $aggregations;
        $this->queryFields = $queryFields;

        foreach ($searchInput as $searchInputKey => $searchInputValue) {
            if (is_array($searchInput[$searchInputKey])) {
                $terms[$searchInputKey] = $searchInputValue;
            }
        }

        // Check to see if category or search page
        if (!$this->isFilterOnCategoryPage($searchInput)) {
            $this->queryType = 'should';
            $this->isBroadSearch = true;
            foreach ($queryFields as $query_field) {
                $terms[$query_field][] = $searchInput[$this->keywordName];
            }
        }

        if ($enforceMustQueryType) {
            $this->queryType = 'must';
        }

        $this->termFields = $terms;

        $this->elasticSearchRequestJSON = '{
       "size": 10000,
       ';
        if (!empty($returnFields)) {
            $this->elasticSearchRequestJSON .= '"fields": [';
            foreach ($returnFields as $field) {
                $this->elasticSearchRequestJSON .= '
            "' . $field . '",';
            }
            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
            $this->elasticSearchRequestJSON .= '
       ],';
        }

        $this->createBroadsearchQuerySearch();

        if ($this->isBroadSearch) {
            $this->createBroadsearchQueryAggregations();
        } else {
            $this->createFilteredQueryAggregations();
        }

        $this->sendElasticsearchRequest();

        return new QueryResults($this->getResults(), $this->getFilters());
    }

    public function createCategoryRequest(
        array $searchInput,
        array $aggregations,
        array $queryFields,
        int $categoryId,
        array $returnFields = [],
        bool $enforceMustQueryType = false
    ): QueryResultsInterface {
        $terms = [];
        $this->aggregations = $aggregations;
        $this->queryFields = $queryFields;

        foreach ($searchInput as $searchInputKey => $searchInputValue) {
            if ($searchInputKey !== 'category_ids' && is_array($searchInput[$searchInputKey])) {
                $terms[$searchInputKey] = $searchInputValue;
            }
        }

        $terms['category_ids'][] = $categoryId;

        if ($enforceMustQueryType) {
            $this->queryType = 'must';
        }

        $this->termFields = $terms;

        $this->elasticSearchRequestJSON = '{
       "size": 10000,
       ';
        if (!empty($returnFields)) {
            $this->elasticSearchRequestJSON .= '"fields": [';
            foreach ($returnFields as $field) {
                $this->elasticSearchRequestJSON .= '
            "' . $field . '",';
            }
            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
            $this->elasticSearchRequestJSON .= '
       ],';
        }

        $this->createCategoryFilterSearch();
        $this->createFilteredQueryAggregations();

        $this->sendElasticsearchRequest();

        return new QueryResults($this->getResults(), $this->getFilters());
    }

    private function sendElasticsearchRequest()
    {
        $endPoint = rtrim($this->url, '/') . '/' . rtrim($this->index, '/') . '/' . $this->method;

        $ch = curl_init($endPoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->elasticSearchRequestJSON);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Content-Type: application/json', 'Content-Length: ' . strlen($this->elasticSearchRequestJSON)]);
        $result = curl_exec($ch);
        $this->responseData = json_decode($result, true);

        if ($this->isBroadSearch) {
            $this->standardiseBroadSearchResults();
        } else {
            $this->standardiseResults();
        }
    }

    public function getFilters() : array
    {
        return $this->filterAggregations;
    }

    public function getResults() : array
    {
        return $this->filterHits;
    }

    private function standardiseResults()
    {
        $this->filterHits = $this->responseData['hits']['hits'];
        $this->filterAggregations = [];

        unset($this->responseData['aggregations']['results']['doc_count']);

        foreach ($this->responseData['aggregations']['results'] as $key => $aggregation) {

            if (isset($aggregation[$key]['buckets'])) {
                $this->filterAggregations[$key] = [
                    'key' => $key,
                    'buckets' => $aggregation[$key]['buckets'],
                    'doc_count' => $aggregation['doc_count']
                ];
            } else {

                $this->filterAggregations[$key] = $aggregation[$key];
            }

        }
    }

    private function standardiseBroadSearchResults()
    {
        $this->filterHits = $this->responseData['hits']['hits'];
        $this->filterAggregations = [];

        foreach ($this->responseData['aggregations'] as $key => $aggregation) {

            $this->filterAggregations[$key] = [
                'label' => str_replace('_', ' ', $key),
                'buckets' => []
            ];

            if (isset($aggregation['buckets'])) {
                foreach ($aggregation['buckets'] as $innerKey => $filter_bucket) {
                    $this->filterAggregations[$key]['buckets'][$innerKey] = [
                        'key' => $filter_bucket['key'],
                        'doc_count' => $filter_bucket['doc_count']
                    ];
                }
            } else {
                $this->filterAggregations[$key] = $aggregation;
            }

        }
    }

    private function createBroadsearchQuerySearch()
    {
        $this->elasticSearchRequestJSON .= '
       "query": {
             "bool": {
                "'.$this->queryType.'": [
        
                  {
                       "multi_match": {
                          "query": "'.$this->keywordValue.'",
                          "fields": [';

                          foreach ($this->queryFields as $queryField) {
                              $this->elasticSearchRequestJSON .= '
                              "'.$queryField.'",';
                          }

                          $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');

                        $this->elasticSearchRequestJSON .= '
                           ]
                       }
                    },';


        foreach ($this->termFields as $keyTerm => $valueTerms) {
            $this->elasticSearchRequestJSON .= '
                    {
                        "bool": {
                            "should": [';
            foreach ($valueTerms as $valueTerm) {

                $this->elasticSearchRequestJSON .= $this->determineValueComparison($valueTerm, $keyTerm);

            }
            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
            $this->elasticSearchRequestJSON .= '
                            ]
                        }
                    },';
        }

        $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
        $this->elasticSearchRequestJSON .= ' 
                ]
             
          }
       }, ';
    }

    private function createCategoryFilterSearch()
    {
        $this->elasticSearchRequestJSON .= '
       "query": {
             "bool": {
                "'.$this->queryType.'": [';

        foreach ($this->termFields as $keyTerm => $valueTerms) {
            $this->elasticSearchRequestJSON .= '
                    {
                        "bool": {
                            "should": [';
            foreach ($valueTerms as $valueTerm) {
                $this->elasticSearchRequestJSON .= $this->determineValueComparison($valueTerm, $keyTerm);
            }
            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
            $this->elasticSearchRequestJSON .= '
                            ]
                        }
                    },';
        }

        $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
        $this->elasticSearchRequestJSON .= ' 
                ]
             
          }
       }, ';
    }

    private function createBroadsearchQueryAggregations()
    {

        $this->elasticSearchRequestJSON .= ' 
       "aggregations": {';

        foreach ($this->aggregations as $keyAggregationName =>$aggregation) {

            $internalKey = (key($aggregation));

            $this->elasticSearchRequestJSON .= '
            "'.$keyAggregationName.'": { 
                "'.$internalKey.'": {
                    "field": "'.$aggregation[$internalKey]['field'].'"  
                }
            },';
        }
        $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');
        $this->elasticSearchRequestJSON .= '
        }   
    }';
    }

    private function createFilteredQueryAggregations()
    {
        $this->elasticSearchRequestJSON .= ' 
           "aggregations": {
               "results": {
                    "global": {}, 
                    "aggregations": {
                        ';

        foreach ($this->aggregations as $key => $aggregation) {

            $internalKey = str_replace('_bucket', '',$key);

            $this->elasticSearchRequestJSON .= '
                        "'.$key.'": {
                                "filter": {
                                    "bool": {
                                        "must": [';

                                        if (!empty($this->keywordValue)) {
                                            $this->elasticSearchRequestJSON .= '
                                            {
                                                "multi_match": {
                                                    "query": "' . $this->keywordValue . '",
                                                    "fields": [';
                                            foreach ($this->queryFields as $queryFieldKey => $queryField) {
                                                $this->elasticSearchRequestJSON .= '
                                                        "' . $queryField . '",';
                                            }

                                            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON,
                                                ',');

                                            $this->elasticSearchRequestJSON .= '
                                                    ]
                                                }
                                            },';
                                        }


            foreach ($this->termFields as $subKey => $subQuery){

                if ($subKey !== $internalKey || $this->forceMustAggregationComparison) {

                    $this->elasticSearchRequestJSON .= $this->determineAggregationComparison($subKey);

                }

            }

            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');

            $this->elasticSearchRequestJSON .= '
                                       
                                        ], 
                                        "must_not": [';


            foreach ($this->termFields as $subKey => $subQuery){

                if ($subKey === $internalKey && $this->forceMustNotAggregationComparison) {

                    $this->elasticSearchRequestJSON .= $this->determineAggregationComparison($subKey);

                }

            }

            $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');


            $this->elasticSearchRequestJSON .= '
                                        ]';

            if (isset($this->mappedFields[$internalKey])) {
                $internalKey = $this->mappedFields[$internalKey];
            }

            //determine comparison
            if (strpos($internalKey, 'price') !== false) {
                $fieldType = 'extended_stats';
            } else {
                $fieldType = 'terms';
            }

            $this->elasticSearchRequestJSON .= '
                                }
                                
                            },
                            "aggregations": {
                                "'.$key.'": {
                                    "'.$fieldType.'": {
                                        "field": "'.$internalKey.'"
                                    }
                                }
                            }
                        },';
        }
        $this->elasticSearchRequestJSON = rtrim($this->elasticSearchRequestJSON, ',');

        $this->elasticSearchRequestJSON .= '
                    }
                }
            }
        }';
    }

    private function determineValueComparison(string $valueTerm, string $keyTerm) : string
    {
        //named ambiguously as hope to refactor to use an array instead of string
        $returnValue = '';

        //determine comparison
        if (strpos($keyTerm, 'price') !== false) {
            $matchType = 'range';
        } else {
            $matchType = 'match';
        }

        //checks to see if the indexed field is to be used
        if (isset($this->mappedFields[$keyTerm])) {
            $keyTerm = $this->mappedFields[$keyTerm];
        }

        if ($matchType === 'match') {
            $returnValue .= '
                                {
                                    "' . $matchType . '": {
                                        "' . $keyTerm . '": "' . $valueTerm . '"
                                    }
                                },';
        } else {

            $returnValue .= '
                                {
                                    "' . $matchType . '": {
                                        "' . $keyTerm . '": {'.
                                            $this->createRangedValues($valueTerm).'
                                        }
                                    }
                                },';
        }


        return $returnValue;
    }

    function determineAggregationComparison($subKey)
    {
        //determine comparison
        if (strpos($subKey, 'price') !== false) {
            $matchType = 'range';
        } else {
            $matchType = 'terms';
        }

        //checks to see if they indexed field is to be used
        if (isset($this->mappedFields[$subKey])) {
            $aggregationKey = $this->mappedFields[$subKey];
        } else {
            $aggregationKey = $subKey;
        }

        $returnValue = '';

        if ($matchType == 'terms') {

            //named ambiguously as hope to refactor to use a stdClass instead of string

            $returnValue .= '
                                            {
                                            "'.$matchType.'": {';
            $returnValue .= '
                                                "'.$subKey.'": [';
            foreach($this->termFields[$subKey] as $subKeyValue) {
                $returnValue .= '"'.$subKeyValue.'",';
            }
            $returnValue = rtrim($returnValue, ',');
            $returnValue .= ']
                                            }
                                        },';
        } else {

            $returnValue .= '
                                            {
                                                "bool": {
                                                    "should": [
                                                        ';
                                                    foreach ($this->termFields[$subKey] as $subKeyValue) {
                                                        $returnValue .= '{
                                                            "range": ';
                                                            $returnValue .= '{
                                                                "'.$aggregationKey.'": {'.
                                                                            $this->createRangedValues($subKeyValue).'
                                                                }
                                                            }
                                                        },
                                                        ';
                                                    }
            $returnValue = rtrim($returnValue, ',
                                                    ');

                                                    $returnValue .= '
                                                    ]
                                                }
                                            },';

        }


        return $returnValue;
    }

    /**
     * Another switch function that sets options due to badly defined class properties - will be remedied in Version2
     * @param bool $switchOption
     */
    public function enforceBroadSearchOption($switchOption)
    {
        $this->isBroadSearch = $switchOption;
    }

    public function createRangedValues($values, $delimiter = '-')
    {

        $valuesAsArray = (explode($delimiter, $values));

        $returnString = '';

        if ($valuesAsArray[0] != '') {
            $returnString .= '
                                                                    "gte": "'.$valuesAsArray[0].'",';
        }

        if ($valuesAsArray[1] != '') {
            $returnString .= '
                                                                    "lt": "'.$valuesAsArray[1].'"';
        }

        $returnString = rtrim($returnString, ',');

        return $returnString;
    }

    public function setMappedQueryFields(array $mappedFields)
    {
        $this->mappedFields = $mappedFields;
    }

    /**
     * @param array $searchInput
     * @return bool
     */
    private function isFilterOnCategoryPage(array $searchInput): bool
    {
        if ((count($searchInput) == 1) && isset($searchInput[$this->keywordName])) {
            return false;
        } else {
            return true;
        }
    }

}