<?php

namespace Limitless\DiscountSplat\Block\Product;

use Magento\Catalog\Model\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    public function getProductDetailsHtml(Product $product)
    {

        $percentage = "";
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