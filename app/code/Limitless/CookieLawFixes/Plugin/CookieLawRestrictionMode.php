<?php

namespace Limitless\CookieLawFixes\Plugin;

use Magento\Cookie\Helper\Cookie;

class CookieLawRestrictionMode
{

    /**
     * @param Cookie $subject
     * @param bool $allowedSaveCookie
     * @return bool
     */
    public function afterIsUserNotAllowSaveCookie(Cookie $subject, bool $notAllowedSaveCookie)
    {
        //Implied consent to allow GTM to work on first page load
        //Message will still display
        return false;
    }

}