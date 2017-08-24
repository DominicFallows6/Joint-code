<?php

namespace Limitless\Delivery\DeliveryApi;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;

abstract class DeliveryApi implements DeliveryApiInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * DeliveryApi constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public abstract function buildRequest(RateRequest $request);

    public abstract function call(RateRequest $request): array;

    public abstract function filterResponse($deliveryOptions, $orderValue): array;

    /**
     * @param $configPath
     * @param bool $sanitise
     * @return mixed
     */
    protected function getConfig($configPath, $sanitise = false)
    {
        $config = $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);

        return $sanitise === true ? str_replace([' ','&','?','='], [''], $config) : $config;
    }

    /**
     * @return array
     */
    protected function offlineDeliveryOption(): array
    {
        $offlineOption = [];
        $offlineOption['deliveryTimeString'] = (__('3 - 5 Working days'));
        $offlineOption['deliveryOptionString'] = $this->getConfig('carriers/delivery_metapack/economy_group', true);
        $offlineOption['deliveryServiceLevelString'] = (__('Standard delivery'));
        $offlineOption['shippingCharge'] = $this->getConfig('carriers/delivery/economy_group_price', true);

        return $offlineOption;
    }
}