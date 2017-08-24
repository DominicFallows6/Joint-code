<?php

namespace Limitless\Delivery\Helper\Metapack;

use Magento\Framework\App\Config\ScopeConfigInterface;

class DmResponse extends AbstractResponse
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * DmResponse constructor.
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

        return $deliveryOption;
    }

    /**
     * @param $deliveryDate
     * @return string
     */
    private function buildDeliveryTimeString($deliveryOption)
    {
        // Hour
        $hourFrom = date("g", strtotime($deliveryOption['deliveryWindow']->from));
        $hourTo = date("g", strtotime($deliveryOption['deliveryWindow']->to));

        // Minutes (rounded down to nearest 0)
        $minutesFrom = date("i", strtotime($deliveryOption['deliveryWindow']->from));
        $minutesFrom = ((int) ($minutesFrom / 10)) * 10;
        if((string) strlen($minutesFrom) == 1) {
            $minutesFrom = '0' . (string) $minutesFrom;
        }
        $minutesTo = date("i", strtotime($deliveryOption['deliveryWindow']->to));
        $minutesTo = ((int) ($minutesTo / 10)) * 10;
        if((string) strlen($minutesTo) == 1) {
            $minutesTo = '0' . (string) $minutesTo;
        }

        // am / pm
        $meridianFrom = date("a", strtotime($deliveryOption['deliveryWindow']->from));
        $meridianTo = date("a", strtotime($deliveryOption['deliveryWindow']->to));

        // Put it altogether and what do you get?
        $deliveryFrom = $hourFrom . ':' . $minutesFrom . $meridianFrom;
        $deliveryTo = $hourTo . ':' . $minutesTo . $meridianTo;

        return $deliveryFrom . ' - ' . $deliveryTo;
    }

    private function buildServiceLevelString($deliveryOption)
    {
        $deliveryDate = strtotime($deliveryOption['deliveryWindow']->to);
        $deliveryTime = date("H", $deliveryDate);

        if ((int)$deliveryTime > 12) {
            return (__('Anytime Delivery'));
        }

        return (__('Morning Delivery Before')) . ' ' . date("g:ia", strtotime($deliveryOption['deliveryWindow']->to));
    }

    /**
     * @param $option
     * @return string
     */
    private function buildDeliveryOptionString($option)
    {
        $acceptableCarrierServiceGroupCode = $option['groupCode'];
        $acceptableCollectionSlots = $option['collectionWindow']->from . ',' . $option['collectionWindow']->to;
        $acceptableDeliverySlots = $option['deliveryWindow']->from . ',' . $option['deliveryWindow']->to;

        return 'acceptableCarrierServiceGroupCodes:' . $acceptableCarrierServiceGroupCode . '|acceptableCollectionSlots:' . $acceptableCollectionSlots . '|acceptableDeliverySlots:' . $acceptableDeliverySlots;
    }
}