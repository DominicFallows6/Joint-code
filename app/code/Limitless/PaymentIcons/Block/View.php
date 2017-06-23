<?php

namespace Limitless\PaymentIcons\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class View extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Template\Context $context,
        array $data = []
    )
    {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context,$data);
    }

    private function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue('general/limitless_payment_icons/' . $path, ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentIcons() {
        $payment_icon = "";
        $payment_values = explode(",",$this->getScopeConfigValue('paymentmethods'));
        foreach($payment_values as $key => $value) {
            $payment_icon .= '<span class="payment_icon ' . $value . '"></span>';
        }
        return $payment_icon;
    }

}