<?php

namespace Limitless\GoogleAnalytics\Observer;

use Limitless\GoogleAnalytics\Helper\DetailProductHelper;
use Magento\Framework\Event\ObserverInterface;

class ClearAndUpdateSessionCartQuantityObserver implements ObserverInterface
{
    /** @var \Magento\GoogleTagManager\Helper\Data */
    protected $helper;

    /** @var \Magento\Checkout\Model\Session */
    protected $checkoutSession;

    /** @var \Magento\Framework\Registry */
    private $registry;

    /** @var DetailProductHelper */
    private $detailProductHelper;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        DetailProductHelper $detailProductHelper
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->registry = $registry;
        $this->detailProductHelper = $detailProductHelper;
    }

    /**
     * When shopping cart is cleaned the remembered quantities in a session needs also to be deleted
     *
     * Fired by controller_action_postdispatch_checkout_cart_updatePost event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isTagManagerAvailable()) {
            return $this;
        }

        /** @var \Magento\Framework\App\Action\Action $controllerAction */
        $controllerAction = $observer->getEvent()->getControllerAction();
        $updateAction = (string)$controllerAction->getRequest()->getParam('update_cart_action');

        if ($updateAction == 'update_qty') {
            $items = $this->checkoutSession->getQuote()->getItems();
            if ($items) {
                $this->detailProductHelper->updateGTMProductRegistry($items);
            }
        }

        if ($updateAction == 'empty_cart') {
            $this->checkoutSession->unsetData(
                \Magento\GoogleTagManager\Helper\Data::PRODUCT_QUANTITIES_BEFORE_ADDTOCART
            );
        }

        return $this;
    }
}
