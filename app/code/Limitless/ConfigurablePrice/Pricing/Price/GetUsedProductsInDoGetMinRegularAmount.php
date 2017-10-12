<?php

namespace Limitless\ConfigurablePrice\Pricing\Price;

use Magento\ConfigurableProduct\Pricing\Price\ConfigurableRegularPrice;

class GetUsedProductsInDoGetMinRegularAmount extends ConfigurableRegularPrice
{
    /**
     * Get min regular amount
     * LDG - Implementing the core fix while it goes through the Magento channels to release.
     * https://github.com/magento/magento2/issues/6729
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    protected function doGetMinRegularAmount()
    {
        $minAmount = null;
        foreach ($this->getUsedProducts() as $product) {
            $childPriceAmount = $product->getPriceInfo()->getPrice(self::PRICE_CODE)->getAmount();
            if (!$minAmount || ($childPriceAmount->getValue() < $minAmount->getValue())) {
                $minAmount = $childPriceAmount;
            }
        }
        return $minAmount;
    }
}