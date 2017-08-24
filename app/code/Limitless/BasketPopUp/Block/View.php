<?php

namespace Limitless\BasketPopUp\Block;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Tax\Model\Config;

class View extends Template

{

    /**
     * @var Config
     */
    protected $taxConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $pricingHelper;

    /**
     * View constructor.
     * @param Context $context
     * @param Data $pricingHelper
     * @param Config $taxConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $pricingHelper,
        Config $taxConfig,
        array $data = []
    )
    {
        parent::__construct($context,$data);
        $this->registry = $context->getRegistry();
        $this->storeManager = $context->getStoreManager();
        $this->pricingHelper = $pricingHelper;
        $this->taxConfig = $taxConfig;

    }

    public function getProduct() {
        return $this->registry->registry('product');
    }

    public function getProductTitle() {
        $productTitle = $this->getProduct()->getName();
        return $productTitle;
    }

    public function getProductImage() {
        $productTitle = $this->getProduct()->getImage();
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $productTitle;
    }

    public function isConfigurableProduct() {
        $productType = $this->getProduct()->getTypeId();
        $optionLabel = __('Options: ');
        if ($productType === 'configurable') {
            return '<div class="product-configurable-options"><span class="label">'. $optionLabel .'</span><span class="options"></span></div>';
        } else {
            return '';
        }
    }

    public function getProductFinalPrice() {
        $specialPrice = $this->getProduct()->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $productType = $this->getProduct()->getTypeId();
        $specialPriceFormatted = $this->pricingHelper->currency($specialPrice, true, false);

        if ($productType === 'configurable') {
            $configurableProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct());
            $configurablePrices = [];
            /** @var Product $value */
            foreach ($configurableProducts as $value) {
                $configurablePrices[] = $value->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            }
            $originalPrice = min($configurablePrices);
            $originalPriceFormatted = $this->pricingHelper->currency($originalPrice, true, false);
        } else {
            $originalPrice =  $this->getProduct()->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            $originalPriceFormatted = $this->pricingHelper->currency($originalPrice, true, false);
        }

        if ($originalPrice > $specialPrice) {
            return '<span class="special-price">' . $specialPriceFormatted . '</span>' . '<span class="old-price">' . $originalPriceFormatted . '</span>';
        } else {
            return '<span class="price">' . $originalPriceFormatted . '</span>';
        }
    }

    public function taxConfigPricesHtml()
    {
        $config = $this->getTotalsConfig();

        if ($config['display_cart_subtotal_excl_tax'] == 1) {
            $subtotalPricesHtml = '<span class="price-wrapper" data-bind="html:getCartParam(\'subtotal_excl_tax\')"></span>';
        } elseif ($config['display_cart_subtotal_excl_tax'] != 1 && $config['display_cart_subtotal_incl_tax'] == 1) {
            $subtotalPricesHtml = '<span class="price-wrapper" data-bind="html:getCartParam(\'subtotal_incl_tax\')"></span>';
        } else {
            $priceIncTax = '<span class="price-wrapper price-including-tax" data-bind="attr: { \'data-label\': $t(\'Incl. Tax\') }, html:getCartParam(\'subtotal_incl_tax\')"></span>';
            $priceExcTax = '<span class="price-wrapper price-excluding-tax" data-bind="attr: { \'data-label\': $t(\'Excl. Tax\') }, html:getCartParam(\'subtotal_excl_tax\')"></span>';
            $subtotalPricesHtml =  $priceIncTax . $priceExcTax;
        }

        return $subtotalPricesHtml;
    }


    /**
     * Get totals config
     *
     * @return array
     */
    protected function getTotalsConfig()
    {
        return [
            'display_cart_subtotal_incl_tax' => (int)$this->taxConfig->displayCartSubtotalInclTax(),
            'display_cart_subtotal_excl_tax' => (int)$this->taxConfig->displayCartSubtotalExclTax(),
        ];
    }

}