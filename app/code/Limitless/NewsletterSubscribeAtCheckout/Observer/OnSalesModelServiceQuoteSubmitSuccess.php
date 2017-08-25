<?php

namespace Limitless\NewsletterSubscribeAtCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Newsletter\Model\Subscriber;
use Limitless\NewsletterSubscribeAtCheckout\Helper\Config as Helper;

class OnSalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $_subscriber;

    /**
     * @var \Limitless\NewsletterSubscribeAtCheckout\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param Subscriber $subscriber
     * @param Helper $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Subscriber $subscriber,
        Helper $helper,
        LoggerInterface $logger
    ) {
        $this->_subscriber = $subscriber;
        $this->_helper = $helper;
        $this->_logger = $logger;
    }

    /**
     * Subscribe to newsletters if customer checked the checkbox
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->getConfigModule('enabled')) {
            $quote = $observer->getQuote();
            if ($quote->getNewsletterSubscribe()) {
                $email = 'undefined';
                try {
                    $email = $quote->getCustomerEmail();
                    $this->_subscriber->subscribe($email);
                } catch (\Exception $e) {
                    $this->_logger->error($e->getMessage() . 'to ' . $email);
                }
            }
        }

        return $this;
    }
}
