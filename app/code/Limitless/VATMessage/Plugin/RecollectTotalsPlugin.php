<?php

namespace Limitless\VATMessage\Plugin;

use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Address;

class RecollectTotalsPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterSave(Address $subject, $result)
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->setTriggerRecollect(true);
        $quote->getResource()->save($quote);
        return $result;
    }
}