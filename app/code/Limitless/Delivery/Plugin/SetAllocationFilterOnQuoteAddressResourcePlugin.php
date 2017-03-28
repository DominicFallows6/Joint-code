<?php

namespace Limitless\Delivery\Plugin;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as AddressModel;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Magento\Framework\Model\AbstractModel;

class SetAllocationFilterOnQuoteAddressResourcePlugin
{
    public function __construct(AllocationFilterFactory $allocationFilterFactory) 
    {
        $this->allocationFilterFactory = $allocationFilterFactory;
    }

    public function aroundSave(
        Address $subject,
        \Closure $proceed,
        AbstractModel $address
    ) {
        $result = $proceed($address);

        /** @var AllocationFilter $allocationFilterModel */
        /** @var AddressModel $address */
        if($address->getAddressType() === AddressModel::ADDRESS_TYPE_SHIPPING) {
            $allocationFilterModel = $this->allocationFilterFactory->create();
            $quoteId = $address->getQuote()->getId();
            $allocationFilterModel->getResource()->load($allocationFilterModel, $quoteId, AllocationFilter::QUOTE_ID);

            if ($allocationFilterModel->getData(AllocationFilter::ALLOCATION_FILTER) !== $address->getShippingMethod()) {
                $allocationFilterModel->setData(AllocationFilter::QUOTE_ID, $quoteId);
                $allocationFilterModel->setData(AllocationFilter::ALLOCATION_FILTER, $this->extractAllocationFilter($address));
                $allocationFilterModel->setData(AllocationFilter::SHIPPING_METHOD, $address->getShippingMethod());
                $allocationFilterModel->getResource()->save($allocationFilterModel);
            }
        }

        return $result;
    }

    public function aroundLoad(
        Address $subject,
        \Closure $proceed,
        AbstractModel $object,
        $value,
        $field = null
    ) {
        $result = $proceed($object, $value, $field);

        /** @var AllocationFilter $allocationFilterModel */
        /** @var AddressModel $address */
        if($address->getAddressType() === AddressModel::ADDRESS_TYPE_SHIPPING) {
            $allocationFilterModel = $this->allocationFilterFactory->create();
            $quoteId = $address->getQuote()->getId();
            $allocationFilterModel->getResource()->load($allocationFilterModel, $quoteId, AllocationFilter::QUOTE_ID);
            $fullAllocationFilter = $allocationFilterModel->getAllocationFilterShippingMethod();
            if ($fullAllocationFilter !== $address->getShippingMethod()) {
                $address->setShippingMethod($fullAllocationFilter);
            }
        }

        return $result;
    }

    private function extractAllocationFilter(AddressModel $address): string
    {
        $parts = explode('_', $address->getShippingMethod(), 2);
        return count($parts) === 2 ? $parts[1] : $address->getShippingMethod();
    }
}
