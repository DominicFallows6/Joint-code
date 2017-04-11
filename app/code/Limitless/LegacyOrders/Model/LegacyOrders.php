<?php


namespace Limitless\LegacyOrders\Model;

use Magento\Framework\Model\AbstractModel;

class LegacyOrders extends AbstractModel
{

    protected function _construct()
    {
        $this->_init(ResourceModel\LegacyOrders::class);
    }

}