<?php

namespace Limitless\CustomCategory\Model;

use Magento\Framework\Model\AbstractModel;

class CustomCategory extends AbstractModel
{
    const META_DESCRIPTION = 'meta_description';

    protected function _construct()
    {
        $this->_init(ResourceModel\CustomCategory::class);
    }
}