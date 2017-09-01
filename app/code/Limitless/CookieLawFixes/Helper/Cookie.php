<?php

namespace Limitless\CookieLawFixes\Helper;

use Magento\Cookie\Helper\Cookie as MagentoCookieHelper;
use Magento\Framework\App\Helper\Context;

class Cookie extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var MagentoCookieHelper
     */
    private $magentoCookieHelper;

    public function __construct(Context $context, MagentoCookieHelper $magentoCookieHelper)
    {
        parent::__construct($context);
        $this->magentoCookieHelper = $magentoCookieHelper;
    }

    /**
     * Check if cookie restriction mode is enabled for this store
     *
     * @param string|null $storeId
     * @return bool
     */
    public function isCookieRestrictionModeEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue(
            MagentoCookieHelper::XML_PATH_COOKIE_RESTRICTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMagentoCookieHelper()
    {
        return $this->magentoCookieHelper;
    }
}
