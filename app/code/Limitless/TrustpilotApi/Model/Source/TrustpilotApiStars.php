<?php

namespace Limitless\TrustpilotApi\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class TrustpilotApiStars implements ArrayInterface
{
    /**
     * @return array
     */
    final public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return [
            '1' => '1 star',
            '2' => '2 star',
            '3' => '3 star',
            '4' => '4 star',
            '5' => '5 star'
        ];
    }
}