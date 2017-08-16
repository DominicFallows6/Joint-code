<?php

namespace Limitless\DiscountSplat\Block;

use Magento\Catalog\Model\Product;
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
        $product = $this->registry->registry('product');
        $specialPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
        $productType = $product->getTypeId();
        $minusSymbol = "-";
        $percentSymbol = "%";

        if ($productType === 'configurable') {
            $configurableProducts = $product->getTypeInstance()->getUsedProducts($product);
            $configurablePrices = [];
            /** @var Product $value */
            foreach ($configurableProducts as $value) {
                $configurablePrices[] = $value->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount();
            }
            $originalPrice = min($configurablePrices);
        } else {
            $originalPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount();
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