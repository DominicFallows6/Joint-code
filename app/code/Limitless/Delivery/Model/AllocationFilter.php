<?php

namespace Limitless\Delivery\Model;

use Magento\Framework\Model\AbstractModel;

class AllocationFilter extends AbstractModel
{
    const QUOTE_ID = 'quote_id';
    const ORDER_ID = 'order_id';
    const ALLOCATION_FILTER = 'allocation_filter';
    const SHIPPING_METHOD = 'shipping_method';

    protected function _construct()
    {
        $this->_init(ResourceModel\AllocationFilter::class);
    }

    public function getAllocationFilterShippingMethod()
    {
        $data = $this->_getData(self::SHIPPING_METHOD);
        return $data ?? $this->getLegacyAllocationFilterWithCarrier();
    }

    private function getLegacyAllocationFilterWithCarrier()
    {
        return $this->_getData(self::ALLOCATION_FILTER);
    }
}