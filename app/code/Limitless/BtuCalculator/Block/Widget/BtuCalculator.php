<?php

namespace Limitless\BtuCalculator\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class BtuCalculator extends Template implements BlockInterface
{

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('Limitless_BtuCalculator::btu-calculator.phtml');
    }

}