<?php

namespace Limitless\ProductPDF\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class PDFBaseUrl implements ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'none',
                'label' => __('None - (Blank)')
            ),
            array(
                'value' => 'base_url',
                'label' => __('Base URL')
            ),
            array(
                'value' => 'base_link_url',
                'label' => __('Base Link URL')
            ),
            array(
                'value' => 'base_static_url',
                'label' => __('Base URL for Static View Files')
            ),
            array(
                'value' => 'base_media_url',
                'label' => __('Base URL for Media Files')
            )
        );
    }
}