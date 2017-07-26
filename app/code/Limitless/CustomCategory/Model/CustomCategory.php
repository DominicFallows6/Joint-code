<?php

namespace Limitless\CustomCategory\Model;

use Magento\Framework\Model\AbstractModel;

class CustomCategory extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel\CustomCategory::class);
    }
}