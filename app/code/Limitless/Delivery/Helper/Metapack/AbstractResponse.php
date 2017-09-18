<?php

namespace Limitless\Delivery\Helper\Metapack;

use Limitless\Delivery\Helper\ResponseInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * AbstractResponse constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $configPath
     * @return string|null
     */
    protected function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    public abstract function buildPremiumDeliveryOption(array $deliveryOption, array $filteredDeliveryOptions): array;

    /**
     * @param int $orderValue
     * @param $deliveryOption
     * @return array
     */
    public function buildEconomyDeliveryOption(int $orderValue, array $economyOption): array
    {
        $economyOption['deliveryTimeString'] = (string) (__('3 - 5 Working days'));
        $economyOption['deliveryOptionString'] = 'acceptableCarrierServiceGroupCodes:' . $this->getConfig('carriers/delivery_metapack/economy_group');
        $economyOption['deliveryServiceLevelString'] = (string) (__("I'm not in a hurry"));
        $economyOption['shippingCharge'] = $this->calculateEconomyDeliveryCharge($economyOption['shippingCharge'], $orderValue);

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