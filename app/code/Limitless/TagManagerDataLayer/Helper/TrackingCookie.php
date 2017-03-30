<?php

namespace Limitless\TagManagerDataLayer\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

class TrackingCookie
{
    /** @var CookieManagerInterface */
    private $cookieManager;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        CookieManagerInterface $cookieManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
    }

    private function getCookieName()
    {
        return $this->scopeConfig->getValue(
            'google/limitless_tagmanager_datalayer/affiliate_tracking/affiliate_cookie_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAffiliateCookie()
    {
        return $this->cookieManager->getCookie($this->getCookieName()) ?? false;
    }
}