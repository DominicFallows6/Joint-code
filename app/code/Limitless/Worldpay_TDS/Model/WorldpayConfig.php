<?php
/**
 * Created by PhpStorm.
 * User: tprocter
 * Date: 24/03/2017
 * Time: 14:16
 */

namespace Limitless\Worldpay_TDS\Model;

use Magento\Backend\Model\Session\Quote;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Worldpay\Payments\Model\Config;

class WorldpayConfig extends Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Session $customerSession
     */
    protected $customerSession;

    public function __construct(
        ScopeConfigInterface $configInterface,
        Session $customerSession,
        Quote $sessionQuote
    ) {
        parent::__construct(
            $configInterface,
            $customerSession,
            $sessionQuote
        );
        $this->scopeConfig = $configInterface;
        $this->customerSession = $customerSession;
        $this->sessionQuote = $sessionQuote;
    }

    private function getConfig($path)
    {
        return $this->scopeConfig->getValue('payment/worldpay_payments_card' . $path, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isLiveMode()
    {
        return $this->getConfig('/mode') == 'live_mode';
    }

    public function isAuthorizeOnly()
    {
        return $this->getConfig('/payment_action') == 'authorize';
    }

    public function saveCard()
    {
        return $this->getConfig('/save_card') && ($this->customerSession->isLoggedIn() || $this->sessionQuote->getCustomerId());
    }

    public function threeDSEnabled()
    {
        return $this->getConfig('/threeds_enabled');
    }

    public function getClientKey()
    {
        if ($this->isLiveMode()) {
            return $this->getConfig('/live_client_key');
        } else {
            return $this->getConfig('/test_client_key');
        }
    }

    public function getServiceKey()
    {
        if ($this->isLiveMode()) {
            return $this->getConfig('/live_service_key');
        } else {
            return $this->getConfig('/test_service_key');
        }
    }

    public function getSettlementCurrency()
    {
        return $this->getConfig('/settlement_currency');
    }

    public function debugMode($code)
    {
        return !!$this->scopeConfig->getValue('payment/'. $code .'/debug', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getPaymentDescription()
    {
        return $this->getConfig('/payment_description');
    }

    public function getLanguageCode()
    {
        return $this->getConfig('/language_code');
    }

    public function getShopCountryCode()
    {
        return $this->getConfig('/shop_country_code');
    }

    public function getSitecodes()
    {
        $sitecodeConfig = $this->getConfig('/sitecodes');
        if ($sitecodeConfig) {
            $siteCodes = unserialize($sitecodeConfig);
            if (is_array($siteCodes)) {
                return $siteCodes;
            }
        }
        return false;
    }
}