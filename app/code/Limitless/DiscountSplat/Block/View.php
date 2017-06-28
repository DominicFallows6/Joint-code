<?php

namespace Limitless\DiscountSplat\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function getSplatDiscount()
    {
        $percentage = "";
        $specialPrice = $this->registry->registry('product')->getFinalPrice();
        $productType = $this->registry->registry('product')->getTypeId();
        $product = $this->registry->registry('product');
        $minusSymbol = "-";
        $percentSymbol = "%";

        if ($productType === 'configurable') {
            $configurableProducts = $product->getTypeInstance()->getUsedProducts($product);
            $configurablePrices = [];
            foreach ($configurableProducts as $value) {
                foreach ($value as $configProduct) {
                    $configurablePrices[] = $configProduct['price'];
                }
            }
            $originalPrice = min($configurablePrices);
        } else {
            $originalPrice = $this->registry->registry('product')->getPrice();
        }

        if ($specialPrice) {
            if ($originalPrice > $specialPrice) {
                $percentage = '<div class="discount-splat"><span>' . $minusSymbol .
                    number_format(($originalPrice - $specialPrice) * 100 / $originalPrice, 0) .
                    $percentSymbol . '</span></div>';
            }
        }

        return $percentage;
    }
}