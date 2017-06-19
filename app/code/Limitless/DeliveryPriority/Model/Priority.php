<?php

namespace Limitless\DeliveryPriority\Model;

use Magento\Framework\Model\AbstractModel;

class Priority extends AbstractModel
{
    const ORDER_ID = 'order_id';
    const DELIVERY_PRIORITY = 'delivery_priority';

    protected function _construct()
    {
        $this->_init(ResourceModel\Priority::class);
    }
}