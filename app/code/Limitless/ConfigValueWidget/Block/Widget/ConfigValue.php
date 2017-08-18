<?php

namespace Limitless\ConfigValueWidget\Block\Widget;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

class ConfigValue extends Template implements BlockInterface
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->setTemplate('Limitless_ConfigValueWidget::configvalue.phtml');
    }

    public function getValue() {
        $path = $this->getData('path');
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }


}