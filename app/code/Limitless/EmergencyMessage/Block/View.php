<?php

namespace Limitless\EmergencyMessage\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface as ScopeInterface;


class View extends Template
{
    public function getEmergencyMessageFromConfig()
    {
        return $this->_scopeConfig->getValue('general/limitless_emergency_message/emergency_message_text',
            ScopeInterface::SCOPE_STORE);
    }

    public function getBackgroundColour()
    {
        return $this->_scopeConfig->getValue('general/limitless_emergency_message/emergency_message_background_colour',
            ScopeInterface::SCOPE_STORE);
    }

    public function getTextColour()
    {
        return $this->_scopeConfig->getValue('general/limitless_emergency_message/emergency_message_text_colour',
            ScopeInterface::SCOPE_STORE);
    }
}