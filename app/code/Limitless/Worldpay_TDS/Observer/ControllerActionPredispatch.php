<?php

namespace Limitless\Worldpay_TDS\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function execute(Event $event)
    {
        if ($this->customerSession->getRegenerateSessionIdOnNextPageLoad(true)) {
            $this->customerSession->regenerateId();
        }
    }
}