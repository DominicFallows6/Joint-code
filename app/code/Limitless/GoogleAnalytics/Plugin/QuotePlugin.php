<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Limitless\GoogleAnalytics\Plugin;

use Magento\Framework\App\RequestInterface;

class QuotePlugin
{
    const OVERRIDE_PRODUCT_QTY_PAGES = ['checkout_cart_index'];
    //checkout_cart_updatePost
    /**
     * @var \Magento\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param \Magento\Quote\Model\Quote $result
     * @return \Magento\Quote\Model\Quote
     */
    public function afterLoad(\Magento\Quote\Model\Quote $subject, $result)
    {

        if (!$this->helper->isTagManagerAvailable()) {
            return $result;
        }

        $fullActionName = '';
        if ($this->request) {
            $fullActionName = $this->request->getFullActionName();
        }

        $productQtys = [];
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($subject->getAllItems() as $quoteItem) {
            $parentQty = 1;
            switch ($quoteItem->getProductType()) {
                case 'bundle':
                case 'configurable':
                    break;
                case 'grouped':
                    $id = $quoteItem->getOptionByCode('product_type')->getProductId()
                        . '-' . $quoteItem->getProductId();
                    $productQtys[$id] = $quoteItem->getQty();
                    break;
                case 'giftcard':
                    $id = $quoteItem->getId() . '-' . $quoteItem->getProductId();
                    $productQtys[$id] = $quoteItem->getQty();
                    break;
                default:
                    if ($quoteItem->getParentItem()) {
                        $parentQty = $quoteItem->getParentItem()->getQty();
                        $id = $quoteItem->getId() . '-' .
                            $quoteItem->getParentItem()->getProductId() . '-' .
                            $quoteItem->getProductId();
                    } else {
                        $id = $quoteItem->getProductId();
                    }
                    $productQtys[$id] = $quoteItem->getQty() * $parentQty;
            }
        }
        /** prevent from overwriting on page load */
        if (!$this->checkoutSession->hasData(
            \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART
        ) || in_array($fullActionName, self::OVERRIDE_PRODUCT_QTY_PAGES)) {
            $this->checkoutSession->setData(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART,
                $productQtys
            );
        }
        return $result;
    }
}