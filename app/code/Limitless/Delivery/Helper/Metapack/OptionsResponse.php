<?php

namespace Limitless\Delivery\Helper\Metapack;

use Magento\Framework\App\Config\ScopeConfigInterface;

class OptionsResponse extends AbstractResponse
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * OptionsResponse constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($scopeConfig);
    }

    /**
     * @param $deliveryOption
     * @param $filteredDeliveryOptions
     * @return array
     */
    public function buildPremiumDeliveryOption(array $deliveryOption, array $filteredDeliveryOptions): array
    {
        $deliveryTimeString = $this->buildDeliveryTimeString($deliveryOption);
        $deliveryOption['deliveryTimeString'] = $deliveryTimeString;
        $deliveryOption['deliveryServiceLevelString'] = $this->buildServiceLevelString($deliveryOption);
        $deliveryOption['deliveryOptionString'] = $this->buildDeliveryOptionString($deliveryOption);
        $filteredDeliveryOptions[] = $deliveryOption;

        return [$deliveryOption, $filteredDeliveryOptions];
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
    private function buildDeliveryOptionString($option)
    {
        $acceptableCarrierServiceGroupCode = $option['groupCodes'][0];
        $acceptableCollectionSlots = $option['collection']['from'] . ',' . $option['collection']['to'];
        $acceptableDeliverySlots = $option['delivery']['from'] . ',' . $option['delivery']['to'];

        return 'acceptableCarrierServiceGroupCodes:' . $acceptableCarrierServiceGroupCode . '|acceptableCollectionSlots:' . $acceptableCollectionSlots . '|acceptableDeliverySlots:' . $acceptableDeliverySlots;
    }
}