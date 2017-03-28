<?php

namespace Limitless\Delivery\Plugin;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\Shipping;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class SetDateInShippingDescriptionPlugin
{
    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    public function __construct(DateTimeFormatterInterface $dateTimeFormatter)
    {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    public function aroundCollect(
        Shipping $subject,
        \Closure $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $result = $proceed($quote, $shippingAssignment, $total);

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();

        if ($method && strpos($method, 'acceptableDeliverySlots') !== false) {

            $datePieces = [];
            preg_match('/acceptableDeliverySlots:(?<date>\d+-\d+-\d+)/', $method, $datePieces);
            if (!empty($datePieces)) {
                $date = $this->dateTimeFormatter->formatObject(new \DateTime($datePieces['date']), 'MMMM d yyyy');

                /** @var \Magento\Quote\Model\Quote\Address\Rate $rate */
                foreach ($address->getAllShippingRates() as $rate) {
                    if ($rate->getCode() === $method) {
                        $shippingDescription = $date . ' (' . $rate->getMethodTitle() . ')';
                        $total->setData('shipping_description', $shippingDescription);
                        break;
                    }
                }
            }

        }

        return $result;
    }
}