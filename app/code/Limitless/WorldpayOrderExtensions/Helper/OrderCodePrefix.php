<?php

namespace Limitless\WorldpayOrderExtensions\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class OrderCodePrefix
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
    public function getOrderPrefixEnabled()
    {
        return $this->scopeConfig->getValue(
            'payment/worldpay_payments_card/enable_wp_order_code_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}