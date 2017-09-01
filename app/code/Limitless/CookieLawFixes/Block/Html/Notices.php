<?php

namespace Limitless\CookieLawFixes\Block\Html;

use Magento\Framework\View\Element\Template;
use Limitless\CookieLawFixes\Helper\Cookie as CookieHelper;

class Notices extends \Magento\Framework\View\Element\Template
{

    /**
     * @var CookieHelper
     */
    private $cookieHelper;

    /**
     * @var String|null
     */
    private $storeId;

    public function __construct(
        CookieHelper $cookieHelper,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cookieHelper = $cookieHelper;
        try {
            $this->storeId = $context->getStoreManager()->getStore()->getStoreId();
        } catch (\Exception $e) {
            $this->storeId = null;
        }
    }

    /**
     * Get Link to cookie restriction privacy policy page
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getPrivacyPolicyLink()
    {
        return $this->_urlBuilder->getUrl('privacy-policy-cookie-restriction-mode');
    }

    public function isCookieRestrictionModeEnabled()
    {
        return $this->cookieHelper->isCookieRestrictionModeEnabled($this->storeId);
    }

    public function getMagentoCookieHelper()
    {
        return $this->cookieHelper->getMagentoCookieHelper();
    }

}