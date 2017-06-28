<?php

namespace Limitless\DiscountSplat\Block\Product;

use Magento\Catalog\Model\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    public function getProductDetailsHtml(Product $product)
    {

        $percentage = "";
        $specialPrice = $product->getFinalPrice();
        $productType = $product->getTypeId();
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
            $originalPrice = $product->getPrice();
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