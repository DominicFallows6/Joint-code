<?php

namespace Limitless\GoogleAnalytics\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class DetailProductHelper
{

    const ATTRIBUTES_TO_LOOKUP = ['select', 'multiselect'];

    const KEY_DATALAYER_NAMES = ['id', 'name', 'category', 'brand', 'variant', 'price', 'quantity'];

    /** @var ScopeConfigInterface */
    private $_scopeConfig;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Product */
    private $product;

    /** @var \Magento\Checkout\Model\Session */
    private $checkoutSession;

    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->registry = $registry;
    }

    /**
     * @param Product|string $lookUpValue
     * @param string $lookUpType
     */
    public function setProduct($lookUpValue, $lookUpType = 'sku')
    {
        switch ($lookUpType)
        {
            case 'object':
                $this->product = $this->productRepository->get($lookUpValue->getSku());
                break;
            case 'sku':
                $this->product = $this->productRepository->get($lookUpValue);
                break;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $items
     * @param null $lastValues
     */
    public function updateGTMProductRegistry($items, $lastValues = null)
    {
        $productsToAddToCart = $this->registry->registry('GoogleTagManager_products_addtocart');
        if (!$productsToAddToCart) {
            $productsToAddToCart = [];
        }

        $productsToRemoveFromCart = $this->registry->registry('GoogleTagManager_products_to_remove');
        if (!$productsToRemoveFromCart) {
            $productsToRemoveFromCart = [];
        }

        $productsRegistry = $this->getProductsToAddAndRemoveFromCart($items, $lastValues, $productsToAddToCart, $productsToRemoveFromCart);

        $updatedReg = false;

        if ($productsRegistry['addToCartUpdated']) {
            $this->registry->unregister('GoogleTagManager_products_addtocart');
            $this->registry->register('GoogleTagManager_products_addtocart', $productsRegistry['addToCart']);
            $updatedReg = true;
        }

        if ($productsRegistry['removeFromCartUpdated']) {
            $this->registry->unregister('GoogleTagManager_products_to_remove');
            $this->registry->register('GoogleTagManager_products_to_remove', $productsRegistry['removeFromCart']);
            $updatedReg = true;
        }

        if ($updatedReg) {
            $this->checkoutSession->unsetData(\Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART);
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $items
     * @param array $lastValues
     * @param array $productsToAddToCart
     * @param array $productsToRemoveFromCart
     * @return array
     */
    private function getProductsToAddAndRemoveFromCart($items, $lastValues, $productsToAddToCart, $productsToRemoveFromCart)
    {
        if (null == $lastValues)
        {
            $lastValues = $this->checkoutSession->getData(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART
            );
        }

        $addToCartUpdated = $removeFromCartUpdated = false;

        foreach ($items as $quoteItem) {
            $id = $quoteItem->getProductId();
            $parentQty = 1;
            $price = $quoteItem->getProduct()->getPrice();
            switch ($quoteItem->getProductType()) {
                case 'configurable':
                case 'bundle':
                    break;
                case 'grouped':
                    $id = $quoteItem->getOptionByCode('product_type')->getProductId() . '-'
                        . $quoteItem->getProductId();
                // no break;
                default:
                    if ($quoteItem->getParentItem()) {
                        $parentQty = $quoteItem->getParentItem()->getQty();
                        $id = $quoteItem->getId() . '-' .
                            $quoteItem->getParentItem()->getProductId() . '-' .
                            $quoteItem->getProductId();

                        if ($quoteItem->getParentItem()->getProductType() == 'configurable') {
                            $price = $quoteItem->getParentItem()->getProduct()->getPrice();
                        }
                    }
                    if ($quoteItem->getProductType() == 'giftcard') {
                        $price = $quoteItem->getProduct()->getFinalPrice();
                    }

                    $oldQty = (array_key_exists($id, $lastValues)) ? $lastValues[$id] : 0;
                    $finalQty = ($parentQty * $quoteItem->getQty()) - $oldQty;

                    //Javascript will now take whatever you pass to it (ie. sku will not convert to id)
                    //If Qty greater than 1 split out
                    $this->setProduct($quoteItem, 'object');
                    $productArray = $this->getProductArrayForDataLayer(['price' => $price]);

                    if ($finalQty != 0) {

                        if ($finalQty > 0) {
                            for ($counter = 0; $counter < $finalQty; $counter++) {
                                $productsToAddToCart[] = $productArray;
                                $addToCartUpdated = true;
                            }
                        } elseif ($finalQty < 0) {
                            for ($counter = 0; $counter > $finalQty; $counter--) {
                                $productsToRemoveFromCart[] = $productArray;
                                $removeFromCartUpdated = true;
                            }
                        }
                    }
            }
        }

        return [
            'addToCartUpdated' => $addToCartUpdated,
            'addToCart' => $productsToAddToCart,
            'removeFromCartUpdated' => $removeFromCartUpdated,
            'removeFromCart' => $productsToRemoveFromCart
        ];
    }

    //Build Product Detail Array
    // {id: sku
    //  name: name
    //  .. (key datalyer names)
    //  .. (optional datalayer)
    //  }
    /**
     * @param array $givenValues
     * @return array
     */
    public function getProductArrayForDataLayer($givenValues = array())
    {
        $productDataLayer = array();

        if ($this->product)
        {
            $productDataLayer['id'] = $this->product->getSku();
            $productDataLayer['name'] = $this->product->getName();
            $productDataLayer['price'] = number_format($this->product->getFinalPrice(), 2);
            $productDataLayer['quantity'] = '1';
            $productDataLayer['category'] = $this->getAttributeLookupValue($this->getProductGACategoryAttributeName(), 'undefined');
            $productDataLayer['brand'] = $this->getAttributeLookupValue($this->getProductGABrandAttributeName(), 'undefined');
            $productDataLayer['variant'] = $this->getAttributeLookupValue($this->getProductGAVariantAttributeName());

            $extraDatalayerValues = $this->getMultipleAttributeValues('array');
            if (!empty($extraDatalayerValues)) {
                $productDataLayer = array_merge($productDataLayer, $extraDatalayerValues);
            }

            //Override with given values if needed (NOTE: could add anything here TODO make sure override?)
            if (!empty($givenValues)) {
                $productDataLayer = array_merge($productDataLayer, $givenValues);
            }
        }

        return $productDataLayer;
    }

    /**
     * @param string $attributeName
     * @return string
     */
    public function getAttributeLookupValue($attributeName = null, $defaultAttributeText = '')
    {
        $attributeValue = null;

        if (!empty($attributeName)) {
            $attributeValue = $this->product->getData($attributeName);
        } else {
            return "";
        }

        try {
            $attributeResource = $this->product->getResource()->getAttribute($attributeName);
            if($attributeResource) {
                $attributeType = $attributeResource->getFrontendInput();
            } else {
                return "";
            }
        } catch (\Exception $e) {
            return "";
        }

        if ($attributeValue && !is_array($attributeValue) && in_array($attributeType, self::ATTRIBUTES_TO_LOOKUP)) {
            try {
                $attributeText =
                    $this->product->getResource()->getAttribute($attributeName)
                        ->getSource()->getOptionText($attributeValue) ?? '';
            } catch (\Exception $e) {
                $attributeText = '';
            }
        } else {
            $attributeText = $attributeValue;
        }

        if (empty($attributeText) && !empty($defaultAttributeText)) {
            $attributeText = $defaultAttributeText;
        }

        return "". $attributeText;
    }

    public function getMultipleAttributeValues($returnAs = 'string')
    {
        /* , "product_detail_extras": "<?= $block->escapeJsQuote($block->getMultipleAttributeValues()); ?>" */

        //array
        $returnAsString = false;
        if ($returnAs == 'string') {
            $returnAsString = true;
        }

        $attributeList = explode(',', $this->getProductGAExtraInformationAttributeName());
        $attributeListString = '';
        $attributeSplit = '::';
        $attributeDelimiter = ',';
        $attributeListArray = [];

        //TODO allow rename (e.g. filter_output[width]


        foreach ($attributeList as $attribute) {
            $attributeValue = "";
            if (!empty($attribute)) {
                $attributeValue = $this->getAttributeLookupValue(trim($attribute));
            }

            if (!empty($attributeValue) && !in_array(trim($attribute), self::KEY_DATALAYER_NAMES)) {
                if ($returnAsString) {
                    $attributeListString .= trim($attribute) . $attributeSplit . trim($attributeValue) . $attributeDelimiter;
                } else {
                    $attributeListArray[trim($attribute)] = trim($attributeValue);
                }
            }
        }

        if (!empty($attributeListString)) {
            $attributeListString = substr($attributeListString, 0, -1);
        }

        if ($returnAsString) {
            return $attributeListString;
        } else {
            return $attributeListArray;
        }
    }

    /**
     * @return string
     */
    public function getProductGACategoryAttributeName()
    {
        return trim($this->getConfigValue('general_settings/category'));
    }

    /**
     * @return string
     */
    public function getProductGAAllowCategoryCookie()
    {
        if ($this->getConfigValue('general_settings/use_category_cookie')){
            return 1;
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getProductGACategoryCookieDefault()
    {
        return trim($this->getConfigValue('general_settings/empty_category_default'));
    }

    /**
     * @return string
     */
    public function getProductGABrandAttributeName()
    {
        return trim($this->getConfigValue('general_settings/brand'));
    }

    /**
     * @return string
     */
    public function getProductGAVariantAttributeName()
    {
        return trim($this->getConfigValue('general_settings/variant'));
    }

    /**
     * @return string
     */
    public function getProductGAExtraInformationAttributeName()
    {
        return trim($this->getConfigValue('general_settings/extra_information'));
    }

    /**
     * @param string $path
     * @return string
     */
    private function getConfigValue($path)
    {
        return $this->_scopeConfig->getValue(
                'google/analytics/limitless_analytics/' . $path . '',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) ?? '';
    }
}