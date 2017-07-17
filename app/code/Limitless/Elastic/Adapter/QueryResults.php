<?php

namespace Limitless\Elastic\Adapter;

class QueryResults implements QueryResultsInterface
{
    /**
     * @var array $results Contains an array of matched results from ES
     */
    private $results;

    /**
     * @var array $filters Contains an array of aggregations / facets from ES
     */
    private $filters;

    public function __construct(array $results, array $filters)
    {
        $this->results = $results;
        $this->filters = $filters;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}