<?php

namespace Limitless\TagManagerDataLayer\Helper\TagsDataLayer;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ProductFactory;

class DynamicRemarketing
{
    const DR_GENERAL_SETTINGS_CONFIGPATH = 'google/limitless_tagmanager_datalayer/dynamic_remarketing/';

    const DR_DATALAYER_NAME = 'google_tag_params';

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var ProductFactory */
    private $productFactory;

    /** @var string */
    private $pagetype;

    /** @var string */
    private $prodcategory;

    /** @var string */
    private $prodid;

    /** @var string */
    private $prodvalue;

    /** @var string */
    private $totalvalue;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->productFactory = $productFactory;
    }

    /**
     * @return array
     */
    public function getAllDynamicRemarketingValuesInArray()
    {
        if ($this->getEnabled()) {
            return [self::DR_DATALAYER_NAME => [
                    'pagetype' => $this->pagetype,
                    'prodcategory' => $this->prodcategory,
                    'prodid' => $this->prodid,
                    'prodvalue' => $this->prodvalue,
                    'totalvalue' => $this->totalvalue,
                ]
            ];
        } else {
            return [];
        }
    }

    /**
     * @param string $pageName
     * @param Product[] $productsArray
     * @param string $categoryName
     * @param string|int $total - Passing -1 will set total to ecommValue (without square brackets)
     * @param array $quantitites
     */
    public function buildAllDynamicRemarketingValues(
        $pageName,
        $productsArray = null,
        $categoryName = '',
        $total = 0,
        $quantitites = null
    ) {

        if ($this->getEnabled()) {
            $ecommProdId = '';
            $ecommValue = 0;

            if ($productsArray != null) {
                $ecommProductValues = $this->getEcommProdIdsAndEcommValueArraysInMatchingOrder(
                    $productsArray,
                    $quantitites
                );

                $ecommProdId = $ecommProductValues['ecommProdId'];
                $ecommValue = $ecommProductValues['ecommProdValue'];
            }

            if ($total == -1) {
                $total = preg_replace('/[^0-9.]/', '', $ecommValue);
            }

            $this->setAllDynamicRemarketingValues(
                $pageName,
                $categoryName,
                $ecommProdId,
                $ecommValue,
                $total
            );
        }
    }

    private function setAllDynamicRemarketingValues(
        $pagetype = '',
        $prodcategory = '',
        $prodid = '',
        $prodvalue = 0,
        $totalvalue = 0)
    {
        $this->pagetype = $pagetype;
        $this->prodcategory = $prodcategory;
        $this->prodid = $prodid;
        $this->prodvalue = $prodvalue;
        $this->totalvalue = $totalvalue;
    }

    /**
     * This is used when the products and values must be returned
     * in separate arrays of "ID" and "price", but must match up in order.
     *
     * ["prod-1", "prod-2", "prod-3"], [price-1, price-2, price-3]
     *
     * @param Product[] $productsArray
     * @param array $quantities
     * @return array
     */
    private function getEcommProdIdsAndEcommValueArraysInMatchingOrder($productsArray, $quantities = null)
    {
        $maxToDisplay = $this->getMaxProductDisplayed();
        $counter = 0;
        $ecommProdIdString = $ecommValueString = '';
        foreach ($productsArray as $key => $product)
        {
            if ($counter >= $maxToDisplay) {
                break;
            }

            $quantity = 1;
            if (isset($quantities[$key])) {
                $quantity = intval($quantities[$key]);
            }
            $ecommProdIdString .= $this->getEcommProdIdsOutputFromProduct($product) . ',';
            $ecommValueString .= $this->getEcommValueOutputFromProduct($product, $quantity) . ',';
            $counter++;
        }
        return [
            'ecommProdId' => '[' . rtrim($ecommProdIdString, ',') . ']',
            'ecommProdValue' => '[' . rtrim($ecommValueString, ',') . ']'
        ];
    }

    /**
     * @param Product $product
     * @return string
     */
    private function getEcommProdIdsOutputFromProduct(Product $product): string
    {
        $productIdValueSetting = $this->getProductIdValue();
        $productPreText = trim($this->getProductIdPrefix());
        switch ($productIdValueSetting) {
            case 'id':
                $ecommProdId = $product->getId();
                break;
            case 'sku':
            default:
                $ecommProdId = htmlspecialchars($product->getSku());
                break;
        }
        return '"'. $productPreText . $ecommProdId. '"';
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return string
     */
    private function getEcommValueOutputFromProduct(Product $product, $quantity = 1): string
    {
        $quantity = intval($quantity);
        $vatSetting = $this->getTotalVatSetting();

        switch ($vatSetting)
        {
            case 'exclude':
                $productPrice = $this->ukNumberFormat(
                    $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount()
                );

                if (strcasecmp($this->getTotalDisplaySummary(), 'total') === 0) {
                    $productPrice = $productPrice * $quantity;
                }

                break;

            case 'include':
            default:
                $productPrice = $this->ukNumberFormat(
                    $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue()
                );

                if (strcasecmp($this->getTotalDisplaySummary(), 'total') === 0) {
                    $productPrice = $productPrice * $quantity;
                }

                break;
        }
        return $this->ukNumberFormat($productPrice);
    }

    /**
     * @param int|float $number
     * @return string
     */
    private function ukNumberFormat($number):string
    {
        return number_format($number, 2);
    }

    public function getMaxProductsDisplayedPublic()
    {
        return $this->getMaxProductDisplayed();
    }

    public function getTotalVatSetting()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('total_vat_setting');
    }

    public function getTotalShippingSetting()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('total_shipping_setting');
    }

    private function getEnabled()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('enabled');
    }

    private function getProductIdValue()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('product_id_value');
    }

    private function getProductIdPrefix()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('product_id_prefix');
    }

    private function getTotalDisplaySummary()
    {
        return $this->getDynamicMarketingGeneralSettingConfig('total_display_summary');
    }

    private function getMaxProductDisplayed()
    {
        return intval($this->getDynamicMarketingGeneralSettingConfig('max_products_displayed')) ?? 3;
    }

    private function getDynamicMarketingGeneralSettingConfig($setting)
    {
        return $this->scopeConfig->getValue(
            self::DR_GENERAL_SETTINGS_CONFIGPATH . $setting,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}