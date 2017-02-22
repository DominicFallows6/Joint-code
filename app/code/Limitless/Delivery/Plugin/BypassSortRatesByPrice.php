<?php

namespace Limitless\Delivery\Plugin;

class BypassSortRatesByPrice
{
    public function aroundSortRatesByPrice(\Magento\Shipping\Model\Rate\Result $subject)
    {
        return $subject;
    }
}