<?php

namespace Limitless\Delivery\Plugin;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\Shipping;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

class SetDateInShippingDescriptionPlugin
{
    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    /**
     * @var State
     */
    private $state;

    public function __construct(
        DateTimeFormatterInterface $dateTimeFormatter,
        State $state
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->state = $state;
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
                        $total->setData('shipping_description', $this->buildShippingDescription($date, $rate));
                        break;
                    }
                }
            }

        }

        return $result;
    }

    /**
     * @param $date
     * @param $rate
     * @return string
     */
    private function buildShippingDescription($date, $rate)
    {
        if ($this->state->getAreaCode() === Area::AREA_ADMINHTML) {
            return $rate->getMethodTitle();
        }

        return $date . ' (' . $rate->getMethodTitle() . ')';
    }
}