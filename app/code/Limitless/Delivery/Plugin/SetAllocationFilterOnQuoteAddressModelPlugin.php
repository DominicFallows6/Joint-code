<?php

namespace Limitless\Delivery\Plugin;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as AddressModel;
use Magento\Quote\Model\Quote\Address;

class SetAllocationFilterOnQuoteAddressModelPlugin
{
    public function __construct(AllocationFilterFactory $allocationFilterFactory)
    {
        $this->allocationFilterFactory = $allocationFilterFactory;
    }

    public function aroundSetQuote(Address $subject, \Closure $proceed, Quote $quote)
    {
        $result = $proceed($quote);

        /** @var AllocationFilter $allocationFilter */
        if ($subject->getAddressType() === AddressModel::ADDRESS_TYPE_SHIPPING) {
            $allocationFilterCode = $this->getAllocationFilterByQuote($quote);
            if ($allocationFilterCode !== $subject->getShippingMethod()) {
                $subject->setShippingMethod($allocationFilterCode);
            }
        }

        return $result;
    }

    private function getAllocationFilterByQuote(Quote $quote)
    {
        $allocationFilter = $this->allocationFilterFactory->create();
        $allocationFilter->getResource()->load($allocationFilter, $quote->getId(), AllocationFilter::QUOTE_ID);

        return $allocationFilter->getAllocationFilterShippingMethod();
    }
}
