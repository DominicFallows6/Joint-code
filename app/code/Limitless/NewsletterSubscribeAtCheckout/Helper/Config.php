<?php

namespace Limitless\NewsletterSubscribeAtCheckout\Helper;

use Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get module settings
     *
     * @param $key
     * @return mixed
     */
    public function getConfigModule($key)
    {
        return $this->scopeConfig
            ->getValue('newsletter/subscription/'.$key, ScopeInterface::SCOPE_STORE);
    }
}
