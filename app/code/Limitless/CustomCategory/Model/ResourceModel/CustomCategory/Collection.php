<?php

namespace Limitless\CustomCategory\Model\ResourceModel\CustomCategory;

use Limitless\CustomCategory\Model\ResourceModel\CustomCategory as CustomCategoryResource;
use Limitless\CustomCategory\Model\CustomCategory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(CustomCategory::class, CustomCategoryResource::class);
    }
}