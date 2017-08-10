<?php

namespace Limitless\TrustpilotApi\Model;

use Magento\Framework\Model\AbstractModel;

class TrustpilotCache extends AbstractModel
{
    const STORE_CODE = 'store_code';
    const BUSINESS_UNITS_CACHE = 'business_units_cache';
    const REVIEW_CACHE = 'review_cache';
    const DATE_CACHE_UPDATED = 'date_cache_updated';

    protected function _construct()
    {
        $this->_init(ResourceModel\TrustpilotCache::class);
    }
}