<?php

namespace Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting;

use Limitless\Delivery\Model\ResourceModel\MetapackCarrierSorting as MetapackCarrierSortingResource;
use Limitless\Delivery\Model\MetapackCarrierSorting;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(MetapackCarrierSorting::class, MetapackCarrierSortingResource::class);
    }
}