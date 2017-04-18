<?php

declare(strict_types=1);

namespace Limitless\WorldpayFixStaticCall\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartManagementInterface;
use Worldpay\Payments\Model\Methods\WorldpayPayments;

class WorldpayStaticMethodCallFixPlugin
{
    /**
     * @var CartManagementInterface
     */
    private $quoteManagement;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        CartManagementInterface $quoteManagement,
        CheckoutSession $checkoutSession
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->checkoutSession = $checkoutSession;
    }

    public function aroundCreateMagentoOrder(WorldpayPayments $subject, \Closure $proceed, $quote)
    {
        try {
            $order = $this->quoteManagement->submit($quote);

            return $order;
        } catch (\Exception $e) {
            $orderId = $quote->getReservedOrderId();
            $payment = $quote->getPayment();
            $payment->getAdditionalInformation('payment_token');
            $amount = $quote->getGrandTotal();
            $payment->setStatus(WorldpayPayments::STATUS_ERROR);
            $payment->setAmount($amount);
            $payment->setLastTransId($orderId);
            $this->_debug($subject, $e->getMessage());

            // ######################################################################
            // This static method call is clearly wrong!
            // This module instead uses a regular call to the session instance method.
            //\Magento\Checkout\Model\Session::restoreQuote();

            // Fixed method call:
            $this->checkoutSession->restoreQuote();
            // ######################################################################

            throw $e;
        }
    }

    protected function _debug(WorldpayPayments $subject, $debugData)
    {
        $method = new \ReflectionMethod($subject, '_debug');
        $method->setAccessible(true);
        $method->invoke($subject, $debugData);
    }
}
