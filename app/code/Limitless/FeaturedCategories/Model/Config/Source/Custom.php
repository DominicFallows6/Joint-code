<?php

namespace Limitless\FeaturedCategories\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Custom implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'one_column', 'label'=>__('One Column')],
            ['value' => 'two_column_side', 'label'=>__('Two Column Side Feature')]
        ];
    }

}