<?php

namespace Limitless\Worldpay_TDS\Plugin;

use Magento\Backend\Block\Template\Context;
use \Magento\Customer\Model\Plugin\CustomerNotification;
use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Customer\NotificationStorage;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\ScopeInterface as ScopeInterface;

class CustomerNotificationPlugin
{
    /** @var Session */
    private $customerSession;

    /** @var NotificationStorage */
    private $notificationStorage;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var State */
    private $state;

    /** @var Context */
    private $context;

    public function __construct(
        Session $session,
        NotificationStorage $notificationStorage,
        State $state,
        CustomerRepositoryInterface $customerRepository,
        Context $context
    ) {
        $this->customerSession = $session;
        $this->notificationStorage = $notificationStorage;
        $this->state = $state;
        $this->customerRepository = $customerRepository;
        $this->context = $context;
    }

    public function aroundBeforeDispatch(
        CustomerNotification $subject,
        \Closure $proceed,
        AbstractAction $subjectOld,
        RequestInterface $request
    ) {

        $usePlugin = $this->context->getScopeConfig()
            ->getValue('payment/worldpay_payments_card/uselimitless3ds', ScopeInterface::SCOPE_WEBSITE);

        if (false == $usePlugin) {
            return $proceed($subjectOld, $request);
        }

        $requestPath = '';
        if ($request instanceof \Magento\Framework\App\Request\Http) {
            $requestPath = $request->getPathInfo();
        }

        if (strcasecmp($requestPath, "/worldpay/threeds/process/") === 0)
        {
            if (
                $this->state->getAreaCode() == Area::AREA_FRONTEND && $request->isPost()
                && $this->notificationStorage->isExists(
                    NotificationStorage::UPDATE_CUSTOMER_SESSION,
                    $this->customerSession->getCustomerId()
                )
            ) {
                $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
                $this->customerSession->setCustomerData($customer);
                $this->customerSession->setCustomerGroupId($customer->getGroupId());
                //Do not regenerate session ID for 3DSecure post -->> $this->session->regenerateId();
                $this->notificationStorage->remove(NotificationStorage::UPDATE_CUSTOMER_SESSION, $customer->getId());
                $this->customerSession->setRegenerateSessionIdOnNextPageLoad(true);
            }
        } else {
            return $proceed($subjectOld, $request);
        }

        return null;
    }
}