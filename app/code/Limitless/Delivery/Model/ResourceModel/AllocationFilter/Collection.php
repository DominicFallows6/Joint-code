<?php

namespace Limitless\Delivery\Model\ResourceModel\AllocationFilter;

use Limitless\Delivery\Model\ResourceModel\AllocationFilter as AllocationFilterResource;
use Limitless\Delivery\Model\AllocationFilter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(AllocationFilter::class, AllocationFilterResource::class);
    }
}