<?php

namespace Limitless\WorldpayOrderExtensions\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class MotoOverride
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int|mixed
     */
    public function getMotoOverrideEnabled($orderStoreId)
    {
        return $this->scopeConfig->getValue(
            'payment/worldpay_payments/enable_wp_moto_override',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $orderStoreId
        );
    }

    public function getMotoOverrideInfo($orderStoreId)
    {
        $motoDetails = [];
        $siteCode = $this->getMotoOverrideSiteCodeConfig($orderStoreId);

        if (! empty($siteCode)) {
            $motoDetails['siteCode'] = $siteCode;
            $motoDetails['settlementCurrency'] = $this->getMotoOverrideSettlementCurrencyConfig($orderStoreId);
        }
        return $motoDetails;
    }

    private function getMotoOverrideSiteCodeConfig($orderStoreId)
    {
        return $this->scopeConfig->getValue(
            'payment/worldpay_payments/wp_moto_override_site_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $orderStoreId
        );
    }

    private function getMotoOverrideSettlementCurrencyConfig($orderStoreId)
    {
        return $this->scopeConfig->getValue(
            'payment/worldpay_payments_card/settlement_currency',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $orderStoreId
        );
    }

}