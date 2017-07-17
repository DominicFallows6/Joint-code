<?php

namespace Limitless\Elastic\Model\Layer\Filter;

class AttributeRewrite extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{
    protected function isOptionReducesResults($optionCount, $totalSize)
    {
        return true;
    }
}