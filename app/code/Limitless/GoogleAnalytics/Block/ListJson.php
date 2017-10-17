<?php

namespace Limitless\GoogleAnalytics\Block;

use Limitless\GoogleAnalytics\Helper\DetailProductHelper;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Json\Helper\Data as FrameworkData;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleTagManager\Helper\Data;
use Magento\GoogleTagManager\Model\Banner\Collector;

class ListJson extends \Magento\GoogleTagManager\Block\ListJson
{
    /** @var \Magento\Catalog\Model\Product */
    private $product;

    /** @var DetailProductHelper */
    private $detailProductHelper;

    public function __construct(
        Context $context,
        Data $helper,
        FrameworkData $jsonHelper,
        Registry $registry,
        Session $checkoutSession,
        CustomerSession $customerSession,
        Cart $checkoutCart,
        Resolver $layerResolver,
        Manager $moduleManager,
        Http $request,
        CollectionFactory $bannerColFactory,
        Collector $bannerCollector,
        DetailProductHelper $detailProductHelper,
        array $data = []
    ) {
        parent::__construct($context, $helper, $jsonHelper, $registry, $checkoutSession, $customerSession,
            $checkoutCart, $layerResolver, $moduleManager, $request, $bannerColFactory, $bannerCollector, $data);

        $this->product = $this->getCurrentProduct();
        $this->detailProductHelper = $detailProductHelper;
    }

    public function initHelper()
    {
        if ($this->product) {
            $this->detailProductHelper->setProduct($this->getCurrentProduct(), 'object');
        }
    }

    /**
     * @param string $attributeName
     * @return string
     */
    public function getAttributeLookupValue($attributeName = null, $defaultAttributeText = '')
    {
        return $this->detailProductHelper->getAttributeLookupValue($attributeName, $defaultAttributeText);
    }


    /**
     * @return array|string
     */
    public function getMultipleAttributeValues()
    {
        return $this->detailProductHelper->getMultipleAttributeValues();
    }

    /**
     * @return string
     */
    public function getProductGACategoryAttributeName()
    {
        return $this->detailProductHelper->getProductGACategoryAttributeName();
    }

    /**
     * @return string
     */
    public function getProductGAAllowCategoryCookie()
    {
        return $this->detailProductHelper->getProductGAAllowCategoryCookie();
    }

    /**
     * @return string
     */
    public function getProductGACategoryCookieDefault()
    {
        return $this->detailProductHelper->getProductGACategoryCookieDefault();
    }
    
    /**
     * @return string
     */
    public function getProductGABrandAttributeName()
    {
        return $this->detailProductHelper->getProductGABrandAttributeName();
    }

    /**
     * @return string
     */
    public function getProductGAVariantAttributeName()
    {
        return $this->detailProductHelper->getProductGAVariantAttributeName();
    }

    /**
     * @return string
     */
    public function getProductGAExtraInformationAttributeName()
    {
        return $this->detailProductHelper->getProductGAExtraInformationAttributeName() ;
    }

}