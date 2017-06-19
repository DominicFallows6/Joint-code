<?php

namespace Limitless\DeliveryPriority\Model\ResourceModel\Priority;

use Limitless\DeliveryPriority\Model\ResourceModel\Priority as PriorityResource;
use Limitless\DeliveryPriority\Model\Priority;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Priority::class, PriorityResource::class);
    }
}