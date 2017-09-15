<?php

namespace Limitless\VATMessage\Observer;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Helper\Address as HelperAddress;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Observer\AfterAddressSaveObserver;
use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Vat;
use Magento\Framework\App\Area;

class AddCheckoutLinkToAfterAddressSaveObserver extends AfterAddressSaveObserver
{
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var AbstractAction
     */
    private $action;

    public function __construct(
        Vat $customerVat,
        HelperAddress $customerAddress,
        Registry $coreRegistry,
        GroupManagementInterface $groupManagement,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager,
        Escaper $escaper,
        AppState $appState,
        CustomerSession $customerSession,
        AbstractAction $action
    ) {
        parent::__construct(
            $customerVat,
            $customerAddress,
            $coreRegistry,
            $groupManagement,
            $scopeConfig,
            $messageManager,
            $escaper,
            $appState,
            $customerSession);
        $this->customerSession = $customerSession;
        $this->action = $action;
    }

    /**
     * Address after save event handler
     * LDG - Add in a check to see if 'vat_id' is in the request. If it's not, then don't display the vat valid/invalid message.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $customerAddress Address */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if (!$this->_customerAddress->isVatValidationEnabled($customer->getStore())
            || $this->_coreRegistry->registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, true);

            if ($customerAddress->getVatId() == ''
                || !$this->_customerVat->isCountryInEU($customerAddress->getCountry())
            ) {
                $defaultGroupId = $this->_groupManagement->getDefaultGroup($customer->getStore())->getId();
                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customer->setGroupId($defaultGroupId);
                    $customer->save();
                    $this->customerSession->setCustomerGroupId($defaultGroupId);
                }
            } else {
                $result = $this->_customerVat->checkVatNumber(
                    $customerAddress->getCountryId(),
                    $customerAddress->getVatId()
                );

                $newGroupId = $this->_customerVat->getCustomerGroupIdBasedOnVatNumber(
                    $customerAddress->getCountryId(),
                    $result,
                    $customer->getStore()
                );

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $newGroupId) {
                    $customer->setGroupId($newGroupId);
                    $customer->save();
                    $this->customerSession->setCustomerGroupId($newGroupId);
                }

                $customerAddress->setVatValidationResult($result);

                if ($this->action->getRequest()->getParam('vat_id')) {
                    if ($this->appState->getAreaCode() == Area::AREA_FRONTEND) {
                        if ($result->getIsValid()) {
                            $this->addValidMessage($customerAddress, $result);
                        } elseif ($result->getRequestSuccess()) {
                            $this->addInvalidMessage($customerAddress);
                        } else {
                            $this->addErrorMessage($customerAddress);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_coreRegistry->register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }

    /**
     * Add success message for valid VAT ID
     *
     * @param Address $customerAddress
     * @param DataObject $validationResult
     * @return $this
     */
    protected function addValidMessage($customerAddress, $validationResult)
    {
        $message = [
            (string)__('Your VAT ID was successfully validated.'),
        ];

        $customer = $customerAddress->getCustomer();
        $checkoutLink = '/checkout/';
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $customerVatClass = $this->_customerVat->getCustomerVatClass(
                $customerAddress->getCountryId(),
                $validationResult
            );
            $message[] = $customerVatClass == Vat::VAT_CLASS_DOMESTIC
                ? (string)__('You will be charged tax. <a href="%1">Continue</a> with your order.',$checkoutLink)
                : (string)__('You will not be charged tax. <a href="%1">Continue</a> with your order.',$checkoutLink);
        }

        $this->messageManager->addSuccess(implode(' ', $message));

        return $this;
    }

    /**
     * Add error message for invalid VAT ID
     *
     * @param Address $customerAddress
     * @return $this
     */
    protected function addInvalidMessage($customerAddress)
    {
        $vatId = $this->escaper->escapeHtml($customerAddress->getVatId());
        $message = [
            (string)__('The VAT ID entered (%1) is not a valid VAT ID.', $vatId),
        ];

        $customer = $customerAddress->getCustomer();
        $checkoutLink = '/checkout/';
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $message[] = (string)__('You will be charged tax. <a href="%1">Continue</a> with your order.',$checkoutLink);
        }

        $this->messageManager->addError(implode(' ', $message));

        return $this;
    }

    /**
     * Add error message
     *
     * @param Address $customerAddress
     * @return $this
     */
    protected function addErrorMessage($customerAddress)
    {
        $message = [
            (string)__('Your Tax ID cannot be validated.'),
        ];

        $customer = $customerAddress->getCustomer();
        $checkoutLink = '/checkout/';
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $message[] = (string)__('You will be charged tax. <a href="%1">Continue</a> with your order.',$checkoutLink);
        }

        $email = $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);
        $message[] = (string)__('If you believe this is an error, please contact us at %1', $email);

        $this->messageManager->addError(implode(' ', $message));

        return $this;
    }
}