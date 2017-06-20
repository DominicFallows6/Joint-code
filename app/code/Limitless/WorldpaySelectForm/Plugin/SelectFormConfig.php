<?php

namespace Limitless\WorldpaySelectForm\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Worldpay\Payments\Model\ConfigProvider;

class SelectFormConfig
{

    const CONFIG_PATH = 'payment/worldpay_payments_card/worldpay_form_code';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    //Add required configuration
    public function afterGetConfig(ConfigProvider $subject, $outConfig)
    {
        $tokenForm = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_WEBSITE);
        if (strlen($tokenForm) < 5)
        {
            $tokenForm = '';
        }

        if (isset($outConfig['payment']['worldpay_payments']) &&
            ! isset($outConfig['payment']['worldpay_payments']['webstore_form_code'])) {

            $outConfig['payment']['worldpay_payments']['webstore_form_code'] = $tokenForm;
        }

        return $outConfig;
    }
}