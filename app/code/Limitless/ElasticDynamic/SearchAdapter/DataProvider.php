<?php

namespace Limitless\ElasticDynamic\SearchAdapter;

use Magento\Elasticsearch\SearchAdapter\Dynamic\DataProvider as Magento2DataProvider;
use Magento\Framework\Search\Dynamic\EntityStorage;

class DataProvider extends Magento2DataProvider
{

    /**
     * {@inheritdoc}
     */
    public function getAggregation(
        \Magento\Framework\Search\Request\BucketInterface $bucket,
        array $dimensions,
        $range,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ): array {
        $result = [];
        $entityIds = $entityStorage->getSource();
        $fieldName = $this->fieldMapper->getFieldName($bucket->getField());
        $dimension = current($dimensions);
        $storeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        $requestQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $this->indexerId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'fields' => [
                    '_id',
                    '_score',
                ],
                'query' => [
                    'constant_score' => [
                        'filter' => [
                            [
                                'terms' => [
                                    '_id' => $entityIds,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'prices' => [
                        'histogram' => [
                            'field' => $fieldName,
                            'interval' => $range,
                        ],
                    ],
                ],
            ],
        ];
        $queryResult = $this->connectionManager->getConnection()
            ->query($requestQuery);
        foreach ($queryResult['aggregations']['prices']['buckets'] as $bucket) {
            $key = intval($bucket['key'] / $range + 1);
            $result[$key] = $bucket['doc_count'];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations(EntityStorage $entityStorage): array
    {
        $aggregations = [
            'count' => 0,
            'max' => 0,
            'min' => 0,
            'std' => 0,
        ];
        $entityIds = $entityStorage->getSource();
        $fieldName = $this->fieldMapper->getFieldName('price');
        $storeId = $this->storeManager->getStore()->getId();
        $requestQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $this->indexerId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'fields' => [
                    '_id',
                    '_score',
                ],
                'query' => [
                    'constant_score' => [
                        'filter' => [
                            [
                                'terms' => [
                                    '_id' => $entityIds,
                                ],
                            ],
                        ],
                    ],
                ],
                'aggregations' => [
                    'prices' => [
                        'extended_stats' => [
                            'field' => $fieldName,
                        ],
                    ],
                ],
            ],
        ];

        $queryResult = $this->connectionManager->getConnection()
            ->query($requestQuery);

        if (isset($queryResult['aggregations']['prices'])) {
            $aggregations = [
                'count' => $queryResult['aggregations']['prices']['count'],
                'max' => $queryResult['aggregations']['prices']['max'],
                'min' => $queryResult['aggregations']['prices']['min'],
                'std' => $queryResult['aggregations']['prices']['std_deviation'],
            ];
        }

        return $aggregations;
    }

}