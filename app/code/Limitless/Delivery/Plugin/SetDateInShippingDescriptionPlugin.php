<?php

namespace Limitless\Delivery\Plugin;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\Shipping;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SetDateInShippingDescriptionPlugin
{
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundCollect(
        Shipping $subject,
        \Closure $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $proceed($quote,$shippingAssignment,$total);

        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();

        if($method && strpos($method, 'acceptableDeliverySlots') !== false) {

            $datePieces = [];
            preg_match('/acceptableDeliverySlots:(\d+-\d+-\d+)/', $method, $datePieces);
            if (!empty($datePieces)) {
                $locale = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE);
                setlocale(LC_TIME, $locale);

                $date = strftime("%B %e %Y", strtotime($datePieces[1]));

                foreach ($address->getAllShippingRates() as $rate) {
                    if ($rate->getCode() == $method) {
                        $shippingDescription = $date . ' (' . $rate->getMethodTitle() . ')';
                        $total->setShippingDescription($shippingDescription);
                        break;
                    }
                }
            }

        }

        return $this;
    }
}