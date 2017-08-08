<?php

namespace Limitless\VATMessage\Observer;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Helper\Address as HelperAddress;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Observer\AfterAddressSaveObserver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Vat;

class AddCheckoutLinkToAfterAddressSaveObserver extends AfterAddressSaveObserver
{
    public function __construct(
        Vat $customerVat,
        HelperAddress $customerAddress,
        Registry $coreRegistry,
        GroupManagementInterface $groupManagement,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager,
        Escaper $escaper,
        AppState $appState,
        CustomerSession $customerSession
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
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $customerVatClass = $this->_customerVat->getCustomerVatClass(
                $customerAddress->getCountryId(),
                $validationResult
            );
            $message[] = $customerVatClass == Vat::VAT_CLASS_DOMESTIC
                ? (string)__('You will be charged tax. <a href="/checkout/">Continue</a> with your order.')
                : (string)__('You will not be charged tax. <a href="/checkout/">Continue</a> with your order.');
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
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $message[] = (string)__('You will be charged tax. <a href="/checkout/">Continue</a> with your order.');
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
        if (!$this->scopeConfig->isSetFlag(HelperAddress::XML_PATH_VIV_DISABLE_AUTO_ASSIGN_DEFAULT)
            && !$customer->getDisableAutoGroupChange()
        ) {
            $message[] = (string)__('You will be charged tax. <a href="/checkout/">Continue</a> with your order.');
        }

        $email = $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE);
        $message[] = (string)__('If you believe this is an error, please contact us at %1', $email);

        $this->messageManager->addError(implode(' ', $message));

        return $this;
    }

}