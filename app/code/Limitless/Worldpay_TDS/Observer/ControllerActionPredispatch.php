<?php

namespace Limitless\Worldpay_TDS\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /** @var CustomerSession */
    private $customerSession;

    /** @var RequestInterface */
    private $request;

    public function __construct(CustomerSession $customerSession, RequestInterface $request)
    {
        $this->customerSession = $customerSession;
        $this->request = $request;
    }

    public function execute(Event $event)
    {
        $requestPath = $this->request->getPathInfo();
        /*
         * Event was being triggered from /worldpay/threeds/process/ event;
         * made sure that it is being triggered on next event.
         */
        if (strcasecmp($requestPath, "/worldpay/threeds/process/") !== 0) {
            if ($this->customerSession->getRegenerateSessionIdOnNextPageLoad(true)) {
                $this->customerSession->regenerateId();
            }
        }
    }
}