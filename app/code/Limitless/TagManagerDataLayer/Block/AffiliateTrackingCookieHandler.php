<?php

namespace Limitless\TagManagerDataLayer\Block;

use Magento\Framework\View\Element\Template;

/**
 * This block is responsible for inserting the javascript on every page
 * that checks if there is an affiliate URL value set.
 * At this point we do not check if it is a valid / enabled affiliate
 *
 * @see Limitless/TagManagerDataLayer/view/frontend/templates/content/cookie_setter.phtml
 */
class AffiliateTrackingCookieHandler extends Template
{
    private function getConfigValue($path)
    {
        return $this->_scopeConfig->getValue(
            'google/limitless_tagmanager_datalayer/affiliate_tracking/' . $path . '',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAffiliateUrlParameter()
    {
        return $this->getConfigValue('affiliate_url_param') ?? 'affid';
    }

    public function getAffiliateCookieName()
    {
        return $this->getConfigValue('affiliate_cookie_name') ?? 'affid_cookie';
    }
}