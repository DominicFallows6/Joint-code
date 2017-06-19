<?php

namespace Limitless\DeliveryPriority\Plugin;

use Limitless\DeliveryPriority\Model\Priority;
use Limitless\DeliveryPriority\Model\PriorityFactory;
use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

class SetPlaceOrderPriorityPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionAttributesFactory;

    /**
     * @var PriorityFactory
     */
    private $priorityFactory;

    /**
     * AddDeliveryPriorityToOrderApiPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param OrderExtensionFactory $orderExtensionAttributesFactory
     * @param PriorityFactory $priorityFactory
     * @param AllocationFilterFactory $allocationFilterFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderExtensionFactory $orderExtensionAttributesFactory,
        PriorityFactory $priorityFactory,
        AllocationFilterFactory $allocationFilterFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderExtensionAttributesFactory = $orderExtensionAttributesFactory;
        $this->priorityFactory = $priorityFactory;
        $this->allocationFilterFactory = $allocationFilterFactory;
    }

    public function afterPlace (
        OrderManagementInterface $subject,
        OrderInterface $order
    ) {
        /** @var OrderInterface $placedOrder */
        $this->setOrderPriority($order);

        return $order;
    }

    /**
     * @param string $configPath
     * @return string|null
     */
    private function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    private function setOrderPriority(OrderInterface $order)
    {
        /** @var Priority $priority */
        $priority = $this->priorityFactory->create();
        $priority->getResource()->load($priority, $order->getEntityId(), Priority::ORDER_ID);
        $shippingData = $this->getShippingData($order);
        $orderPriority = $this->determineOrderPriority($shippingData, $order);
        $priority->addData(
            [
                Priority::ORDER_ID  => $order->getEntityId(),
                Priority::DELIVERY_PRIORITY => $orderPriority
            ]
        );
        $priority->save();
    }

    /**
     * @param $shippingData
     * @param OrderInterface $order
     * @return bool|null|string
     */
    private function determineOrderPriority($shippingData, OrderInterface $order)
    {
        $deliveryType = $this->determineDeliveryType($shippingData);
        $aftersales = $order->getBaseSubtotal() == '0.00' ? true : false;
        $saturdayDelivery = $this->isSaturdayDelivery($shippingData);
        $paidFor = $order['base_shipping_amount'] == '0.00' ? false : true;
        $destination = $this->determineOrderDestination($order);

        $priority = $this->isTimedDelivery($deliveryType);
        $priority = $priority != false ? $priority : $this->isPaidForAndPremiumOrSaturdayDelivery($paidFor, $saturdayDelivery, $deliveryType);
        $priority = $priority != false ? $priority : $this->isPaidForAfterSalesDelivery($aftersales, $saturdayDelivery, $deliveryType);
        $priority = $priority != false ? $priority : $this->isPaidForUKEconomyDelivery($deliveryType, $paidFor, $destination);
        $priority = $priority != false ? $priority : $this->isPaidForNonUKEconomyDelivery($deliveryType, $paidFor, $destination);
        $priority = $priority != false ? $priority : $this->isAftersalesEconomyDelivery($aftersales, $deliveryType);
        $priority = $priority != false ? $priority : $this->isNonPaidForUKPremiumDelivery($paidFor, $destination, $saturdayDelivery, $deliveryType);
        $priority = $priority != false ? $priority : $this->isNonPaidForEUPremiumDelivery($paidFor, $destination, $deliveryType);
        $priority = $priority != false ? $priority : $this->isNonPaidForRestOfWorldPremiumDelivery($paidFor, $destination, $deliveryType);
        $priority = $priority != false ? $priority : $this->isNonPaidForUKEconomyDelivery($paidFor, $deliveryType, $destination);
        $priority = $priority != false ? $priority : $this->isNonPaidForRestOfWorldEconomyDelivery($paidFor, $destination, $deliveryType);
        $priority = $priority != false ? $priority : $this->isNonPaidForEUEconomyDelivery($paidFor, $deliveryType, $destination);

        return $priority;
    }

    /**
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isTimedDelivery($deliveryType)
    {
        if ($deliveryType === 'timed') {
            return $this->getConfig('carriers/delivery_priority/timed');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $saturdayDelivery
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isPaidForAndPremiumOrSaturdayDelivery($paidFor, $saturdayDelivery, $deliveryType)
    {
        if ($paidFor === true && ($saturdayDelivery === true || $deliveryType !== 'economy')) {
            return $this->getConfig('carriers/delivery_priority/all_premium');
        }
        return false;
    }

    /**
     * @param $aftersales
     * @param $saturdayDelivery
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isPaidForAfterSalesDelivery($aftersales, $saturdayDelivery, $deliveryType)
    {
        if ($aftersales === true && ($saturdayDelivery === true || $deliveryType !== 'economy')) {
            return $this->getConfig('carriers/delivery_priority/aftersales_nextday');
        }
        return false;
    }

    /**
     * @param $deliveryType
     * @param $paidFor
     * @param $destination
     * @return bool|null|string
     */
    private function isPaidForUKEconomyDelivery($deliveryType, $paidFor, $destination)
    {
        if ($deliveryType === 'economy' && $paidFor === true && $destination === 'GB') {
            return $this->getConfig('carriers/delivery_priority/uk_eco_paid');
        }
        return false;
    }

    /**
     * @param $deliveryType
     * @param $paidFor
     * @param $destination
     * @return bool|null|string
     */
    private function isPaidForNonUKEconomyDelivery($deliveryType, $paidFor, $destination)
    {
        if ($deliveryType === 'economy' && $paidFor === true && $destination !== 'GB') {
            return $this->getConfig('carriers/delivery_priority/non_uk_eco_paid');
        }
        return false;
    }

    /**
     * @param $aftersales
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isAftersalesEconomyDelivery($aftersales, $deliveryType)
    {
        if ($aftersales === true && $deliveryType === 'economy') {
            return $this->getConfig('carriers/delivery_priority/aftersales_eco');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $destination
     * @param $saturdayDelivery
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isNonPaidForUKPremiumDelivery($paidFor, $destination, $saturdayDelivery, $deliveryType)
    {
        if ($paidFor === false && $destination === 'GB' && ($saturdayDelivery === true || $deliveryType !== 'economy')) {
            return $this->getConfig('carriers/delivery_priority/uk_non_paid_premium');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $destination
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isNonPaidForEUPremiumDelivery($paidFor, $destination, $deliveryType)
    {
        if ($paidFor === false && $destination === 'EU' && $deliveryType !== 'economy') {
            return $this->getConfig('carriers/delivery_priority/eu_non_paid_premium');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $destination
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isNonPaidForRestOfWorldPremiumDelivery($paidFor, $destination, $deliveryType)
    {
        if ($paidFor === false && $destination === 'ROW' && $deliveryType !== 'economy') {
            return $this->getConfig('carriers/delivery_priority/row_non_paid_premium');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $deliveryType
     * @param $destination
     * @return bool|null|string
     */
    private function isNonPaidForUKEconomyDelivery($paidFor, $deliveryType, $destination)
    {
        if ($paidFor === false && $deliveryType === 'economy' && $destination === 'GB') {
            return $this->getConfig('carriers/delivery_priority/uk_eco_free');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $destination
     * @param $deliveryType
     * @return bool|null|string
     */
    private function isNonPaidForRestOfWorldEconomyDelivery($paidFor, $destination, $deliveryType)
    {
        if ($paidFor === false && $destination === 'ROW' && $deliveryType === 'economy') {
            return $this->getConfig('carriers/delivery_priority/row_eco_free');
        }
        return false;
    }

    /**
     * @param $paidFor
     * @param $deliveryType
     * @param $destination
     * @return bool|null|string
     */
    private function isNonPaidForEUEconomyDelivery($paidFor, $deliveryType, $destination)
    {
        if ($paidFor === false && $deliveryType === 'economy' && $destination === 'EU') {
            return $this->getConfig('carriers/delivery_priority/eu_eco_free');
        }
        return false;
    }


    /**
     * @param $shippingData
     * @return string
     */
    private function determineDeliveryType($shippingData)
    {
        $timedGroups = explode(',', $this->getConfig('carriers/delivery/timed_groups'));
        $premiumGroups = explode(',', $this->getConfig('carriers/delivery/premium_groups'));

        foreach ($timedGroups as $timedGroup) {
            if ($shippingData['acceptableCarrierServiceGroupCodes'] === $timedGroup) {
                return 'timed';
            }
        }

        foreach ($premiumGroups as $premiumGroup) {
            if ($shippingData['acceptableCarrierServiceGroupCodes'] === $premiumGroup) {
                return 'premium';
            }
        }

        return 'economy';
    }

    /**
     * @param $shippingData
     * @return bool
     */
    private function isSaturdayDelivery($shippingData): bool
    {
        if (isset($shippingData['acceptableDeliverySlots'])) {
            $date = explode('T', $shippingData['acceptableDeliverySlots'])[0];
            $weekday = date('N', strtotime($date));
            if ($weekday === 6) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $orderId
     * @return array
     */
    private function getShippingData($order): array
    {
        $shippingData = [];
        if ($order['shipping_method'] != '') {
            $shippingMethod = explode('_', $order['shipping_method'])[1];
            $allocationFilters = explode('|', $shippingMethod);
            foreach ($allocationFilters as $allocationFilter) {
                $shippingDataPieces = explode(':', $allocationFilter);
                $shippingData[$shippingDataPieces[0]] = $shippingDataPieces[1];
            }
        }
        return $shippingData;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function determineOrderDestination(OrderInterface $order): string
    {
        $shippingAddress = $order->getShippingAddress();
        $euCountryCodes = array('AD', 'AL', 'AT', 'AX', 'BA', 'BE', 'BG', 'BY', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FO', 'FR', 'GB', 'GG', 'GI', 'GR', 'HR', 'HU', 'IE', 'IM', 'IS', 'IT', 'JE', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SJ', 'SK', 'SM', 'UA', 'VA');
        $orderCountryCode = $shippingAddress['country_id'];

        if ($orderCountryCode === 'GB') {
            return 'GB';
        } else if (in_array($orderCountryCode, $euCountryCodes)) {
            return 'EU';
        } else if ($orderCountryCode === 'US' || $orderCountryCode === 'CA') {
            return 'US';
        }

        return 'ROW';
    }

}