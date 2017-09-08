<?php

namespace Limitless\VATMessage\Model;

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\GroupManagementInterface as CustomerGroupManagement;
use Magento\Customer\Api\GroupRepositoryInterface as CustomerGroupRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\Config;
use Magento\Customer\Api\Data\AddressInterface as CustomerAddress;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\Data\RegionInterface as AddressRegion;

class CalculationOverride extends Calculation
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Config $taxConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory $classesFactory,
        \Magento\Tax\Model\ResourceModel\Calculation $resource,
        CustomerAccountManagement $customerAccountManagement,
        CustomerGroupManagement $customerGroupManagement,
        CustomerGroupRepository $customerGroupRepository,
        CustomerRepository $customerRepository,
        PriceCurrencyInterface $priceCurrency,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        TaxClassRepositoryInterface $taxClassRepository,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $scopeConfig,
            $taxConfig,
            $storeManager,
            $customerSession,
            $customerFactory,
            $classesFactory,
            $resource,
            $customerAccountManagement,
            $customerGroupManagement,
            $customerGroupRepository,
            $customerRepository,
            $priceCurrency,
            $searchCriteriaBuilder,
            $filterBuilder,
            $taxClassRepository,
            $resourceCollection,
            $data
        );
    }

    /**
     * Get request object with information necessary for getting tax rate
     *
     * Request object contain:
     *  country_id (->getCountryId())
     *  region_id (->getRegionId())
     *  postcode (->getPostcode())
     *  customer_class_id (->getCustomerClassId())
     *  store (->getStore())
     *
     * @param null|bool|\Magento\Framework\DataObject|CustomerAddress $shippingAddress
     * @param null|bool|\Magento\Framework\DataObject|CustomerAddress $billingAddress
     * @param null|int $customerTaxClass
     * @param null|int|\Magento\Store\Model\Store $store
     * @param int $customerId
     * @return  \Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getRateRequest(
        $shippingAddress = null,
        $billingAddress = null,
        $customerTaxClass = null,
        $store = null,
        $customerId = null
    )
    {
        if ($shippingAddress === false && $billingAddress === false && $customerTaxClass === false) {
            return $this->getRateOriginRequest($store);
        }
        $address = new \Magento\Framework\DataObject();
        $basedOn = $this->_scopeConfig->getValue(
            \Magento\Tax\Model\Config::CONFIG_XML_PATH_BASED_ON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($shippingAddress === false && $basedOn == 'shipping' || $billingAddress === false && $basedOn == 'billing'
        ) {
            $basedOn = 'default';
        } else {

            if (($billingAddress === null || !$billingAddress->getCountryId())
                && $basedOn == 'billing'
                || ($shippingAddress === null || !$shippingAddress->getCountryId())
                && $basedOn == 'shipping'
            ) {
                if ($customerId) {
                    //fallback to default address for registered customer
                    try {
                        $defaultBilling = $this->customerAccountManagement->getDefaultBillingAddress($customerId);
                    } catch (NoSuchEntityException $e) {
                    }

                    try {
                        $defaultShipping = $this->customerAccountManagement->getDefaultShippingAddress($customerId);
                    } catch (NoSuchEntityException $e) {
                    }

                    if ($basedOn == 'billing' && isset($defaultBilling) && $defaultBilling->getCountryId()) {
                        $billingAddress = $defaultBilling;
                    } elseif ($basedOn == 'shipping' && isset($defaultShipping) && $defaultShipping->getCountryId()) {
                        $shippingAddress = $defaultShipping;
                    } else {
                        $basedOn = 'default';
                    }
                } else {
                    //fallback for guest
                    if ($basedOn == 'billing' && is_object($shippingAddress) && $shippingAddress->getCountryId()) {
                        $billingAddress = $shippingAddress;
                    } elseif ($basedOn == 'shipping' && is_object($billingAddress) && $billingAddress->getCountryId()) {
                        $shippingAddress = $billingAddress;
                    } else {
                        $basedOn = 'default';
                    }
                }
            }
        }

        switch ($basedOn) {
            case 'billing':
                $address = $billingAddress;
                break;
            case 'shipping':
                $address = $shippingAddress;
                break;
            case 'origin':
                $address = $this->getRateOriginRequest($store);
                break;
            case 'default':
                $address->setCountryId(
                    $this->_scopeConfig->getValue(
                        \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store
                    )
                )->setRegionId(
                    $this->_scopeConfig->getValue(
                        \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_REGION,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store
                    )
                )->setPostcode(
                    $this->_scopeConfig->getValue(
                        \Magento\Tax\Model\Config::CONFIG_XML_PATH_DEFAULT_POSTCODE,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $store
                    )
                );
                break;
            default:
                break;
        }

        /**
         * LDG - Removed the if statement which checks if the $customerTaxClass is null or false
         * If the customer id isn't set then it gets the tax class id from the customer session
         * This is a work around fix for magento issue https://github.com/magento/magento2/issues/10567
         */
         if ($customerId) {
             $customerData = $this->customerRepository->getById($customerId);
             $customerTaxClass = $this->customerGroupRepository
                 ->getById($customerData->getGroupId())
                 ->getTaxClassId();
         } else {
             $customerTaxClass = $this->_customerSession->getCustomer()->getTaxClassId();
         }

        $request = new \Magento\Framework\DataObject();
        //TODO: Address is not completely refactored to use Data objects
        if ($address->getRegion() instanceof AddressRegion) {
            $regionId = $address->getRegion()->getRegionId();
        } else {
            $regionId = $address->getRegionId();
        }
        $request->setCountryId($address->getCountryId())
            ->setRegionId($regionId)
            ->setPostcode($address->getPostcode())
            ->setStore($store)
            ->setCustomerClassId($customerTaxClass);
        return $request;
    }
}
