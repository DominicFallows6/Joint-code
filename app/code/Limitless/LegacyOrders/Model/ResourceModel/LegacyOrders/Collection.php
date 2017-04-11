<?php


namespace Limitless\LegacyOrders\Model\ResourceModel\LegacyOrders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Limitless\LegacyOrders\Model\ResourceModel\LegacyOrders as LegacyOrdersResource;
use Limitless\LegacyOrders\Model\LegacyOrders;

class Collection extends AbstractCollection
{

    public function _construct()
    {
        $this->_init(LegacyOrders::class, LegacyOrdersResource::class);
    }

}