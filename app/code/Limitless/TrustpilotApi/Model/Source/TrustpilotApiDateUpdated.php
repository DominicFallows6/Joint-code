<?php

namespace Limitless\TrustpilotApi\Model\Source;

use Limitless\TrustpilotApi\Model\TrustpilotCacheFactory;
use Limitless\TrustpilotApi\Model\ResourceModel\TrustpilotCache as TrustpilotCacheResource;
use Limitless\TrustpilotApi\Model\TrustpilotCache;
use Magento\Framework\Option\ArrayInterface;

class TrustpilotApiDateUpdated implements ArrayInterface
{
    /** @var TrustpilotCacheFactory */
    private $trustpilotCacheFactory;

    public function __construct(TrustpilotCacheFactory $trustpilotCacheFactory)
    {
        $this->trustpilotCacheFactory = $trustpilotCacheFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var TrustpilotCache $trustpilotCacheModel */
        $trustpilotCacheModel = $this->trustpilotCacheFactory->create();

        $date = 'Not yet set';

        try {
            $trustpilotCacheModel->getResource()->load($trustpilotCacheModel, 1, TrustpilotCacheResource::ID_FIELD);

            if ($trustpilotCacheModel->getId()){
                $date = $trustpilotCacheModel->getData($trustpilotCacheModel::DATE_CACHE_UPDATED);
            }

        } catch (\Exception $e) {
            $date = 'Not yet set';
        }

        return
            [
                [
                    'value' => 'date',
                    'label' => $date
                ]
            ];
    }
}