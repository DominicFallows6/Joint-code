<?php

namespace Limitless\SocialMediaLinks\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Custom implements ArrayInterface
{
    public function toOptionArray()
    {
       return [
         ['value' => 'white', 'label' => __('White')],
         ['value' => 'black', 'label' => __('Black')],
         ['value' => 'gray', 'label' => __('Gray')]
       ];
    }

}