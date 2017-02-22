<?php

namespace Limitless\Delivery\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class MetapackResponse
{
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $configPath
     * @return string|null
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $deliveryOption
     * @param $filteredDeliveryOptions
     * @return array
     */
    public function buildPremiumDeliveryOption($deliveryOption, $filteredDeliveryOptions): array
    {
        $deliveryTimeString = $this->buildDeliveryTimeString($deliveryOption);
        $deliveryOption['deliveryTimeString'] = $deliveryTimeString;
        $deliveryOption['deliveryServiceLevelString'] = $this->buildServiceLevelString($deliveryOption);
        $deliveryOption['allocationFilter'] = $this->buildAllocationFilter($deliveryOption);
        $filteredDeliveryOptions[] = $deliveryOption;

        return array($deliveryOption, $filteredDeliveryOptions);
    }

    /**
     * @param $deliveryDate
     * @return string
     */
    private function buildDeliveryTimeString($deliveryOption)
    {
        // Hour
        $hourFrom = date("g", strtotime($deliveryOption['delivery']['from']));
        $hourTo = date("g", strtotime($deliveryOption['delivery']['to']));

        // Minutes (rounded down to nearest 0)
        $minutesFrom = date("i", strtotime($deliveryOption['delivery']['from']));
        $minutesFrom = ((int) ($minutesFrom / 10)) * 10;
        if((string) strlen($minutesFrom) == 1) {
            $minutesFrom = '0' . (string) $minutesFrom;
        }
        $minutesTo = date("i", strtotime($deliveryOption['delivery']['to']));
        $minutesTo = ((int) ($minutesTo / 10)) * 10;
        if((string) strlen($minutesTo) == 1) {
            $minutesTo = '0' . (string) $minutesTo;
        }

        // am / pm
        $meridianFrom = date("a", strtotime($deliveryOption['delivery']['from']));
        $meridianTo = date("a", strtotime($deliveryOption['delivery']['to']));

        // Put it altogether and what do you get?
        $deliveryFrom = $hourFrom . ':' . $minutesFrom . $meridianFrom;
        $deliveryTo = $hourTo . ':' . $minutesTo . $meridianTo;

        return $deliveryFrom . ' - ' . $deliveryTo;
    }

    private function buildServiceLevelString($deliveryOption)
    {
        $deliveryDate = strtotime($deliveryOption['delivery']['to']);
        $deliveryTime = date("H", $deliveryDate);

        if ((int)$deliveryTime > 12) {
            return (__('Anytime Delivery'));
        }

        return (__('Morning Delivery Before')) . ' ' . date("g:ia", strtotime($deliveryOption['delivery']['to']));
    }

    /**
     * @param $option
     * @return string
     */
    private function buildAllocationFilter($option)
    {
        $acceptableCarrierServiceGroupCodes = $option['groupCodes'][0];
        $acceptableCollectionSlots = $option['collection']['from'] . ',' . $option['collection']['to'];
        $acceptableDeliverySlots = $option['delivery']['from'] . ',' . $option['delivery']['to'];

        return 'acceptableCarrierServiceGroupCodes:' . $acceptableCarrierServiceGroupCodes . '|acceptableCollectionSlots:' . $acceptableCollectionSlots . '|acceptableDeliverySlots:' . $acceptableDeliverySlots;
    }

    /**
     * @param int $orderValue
     * @param $deliveryOption
     * @return mixed
     */
    public function buildEconomyDeliveryOption(int $orderValue, $deliveryOption)
    {
        $economyOption = $deliveryOption;
        $economyOption['deliveryTimeString'] = (__('3 - 5 Working days'));
        $economyOption['allocationFilter'] = $this->getConfig('carriers/delivery/economy_group');
        $economyOption['deliveryServiceLevelString'] = (__("I'm not in a hurry"));
        $economyOption['shippingCharge'] = $this->calculateEconomyDeliveryCharge($economyOption['shippingCharge'],
            $orderValue);
        return $economyOption;
    }

    /**
     * @param $shippingCharge
     * @param $orderValue
     * @return int
     */
    private function calculateEconomyDeliveryCharge($shippingCharge, $orderValue)
    {
        $deliveryThreshold = $this->getConfig('carriers/delivery/delivery_charge_threshold');

        if ($orderValue >= $deliveryThreshold) {
            $shippingCharge = 0;
        }

        return $shippingCharge;
    }
}