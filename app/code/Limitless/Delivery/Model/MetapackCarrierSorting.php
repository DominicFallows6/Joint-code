<?php

namespace Limitless\Delivery\Model;

use Magento\Framework\Model\AbstractModel;

class MetapackCarrierSorting extends AbstractModel
{
    const CODE = 'code';
    const SORT_REF_NAME = 'sort_ref_name';

    protected function _construct()
    {
        $this->_init(ResourceModel\MetapackCarrierSorting::class);
    }
}