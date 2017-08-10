<?php

namespace Limitless\TrustpilotApi\Model\ResourceModel\TrustpilotCache;

use Limitless\TrustpilotApi\Model\ResourceModel\TrustpilotCache as TrustpilotCacheResource;
use Limitless\TrustpilotApi\Model\TrustpilotCache;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(TrustpilotCache::class, TrustpilotCacheResource::class);
    }
}