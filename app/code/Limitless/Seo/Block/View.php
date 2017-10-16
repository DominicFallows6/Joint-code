<?php

namespace Limitless\Seo\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class View extends Template
{

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->registry = $registry;
        $this->storeManager = $context->getStoreManager();
    }

    public function getProductSchema() {
        $schemaCondition = "<meta itemprop='itemCondition' content='http://schema.org/NewCondition' />";
        return $schemaCondition;
    }

    public function getOffersSchema() {

        $finalPrice = (float) $this->registry->registry('product')->getFinalPrice();
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $inStock = $this->registry->registry('product')->isSaleable();

        $schemaPriceHtml = "<meta itemprop='price' content='$finalPrice' />";
        $schemaCurrencyCodeHtml = "<meta itemprop='priceCurrency' content='$currencyCode' />";

        if($inStock) {
            $stockSchemaHtml = "<link itemprop='availability' href='http://schema.org/InStock' />";
        } else {
            $stockSchemaHtml = "<link itemprop='availability' href='http://schema.org/OutOfStock' />";
        }

        return $schemaPriceHtml . $schemaCurrencyCodeHtml . $stockSchemaHtml;

    }
}