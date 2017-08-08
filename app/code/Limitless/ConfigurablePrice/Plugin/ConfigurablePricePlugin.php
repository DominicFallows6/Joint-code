<?php

namespace Limitless\ConfigurablePrice\Plugin;


use Magento\ConfigurableProduct\Pricing\Price\ConfigurablePriceResolver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\SaleableInterface;

class ConfigurablePricePlugin
{
    /**
     * @param ConfigurablePriceResolver $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $product
     * @return float|null
     */
    public function aroundResolvePrice(ConfigurablePriceResolver $subject, \Closure $proceed, SaleableInterface $product)
    {
        $result = $proceed($product);
        return $result ?? $product->getData($product::PRICE);
    }

}