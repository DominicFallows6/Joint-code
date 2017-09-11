<?php

namespace Limitless\InvoiceVat\Block;


use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Variable\Model\VariableFactory;

class View extends Template
{
    /**
     * @var VariableFactory
     */
    protected $_varFactory;

    public function __construct(
        VariableFactory $varFactory,
        Context $context)
    {
        $this->_varFactory = $varFactory;
        parent::__construct($context);
    }

    public function getVariableValue($variable, $storeId) {
        $var = $this->_varFactory->create();
        $var->setStoreId($storeId);
        $var->loadByCode($variable);
        return $var->getValue('html');
    }
}