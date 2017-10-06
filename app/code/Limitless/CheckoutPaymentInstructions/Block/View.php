<?php

namespace Limitless\CheckoutPaymentInstructions\Block;

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
        Context $context,
        VariableFactory $varFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_varFactory = $varFactory;
    }

    public function getBankTransferCustomVariableValue($variable, $storeId) {
        $var = $this->_varFactory->create();
        $var->setStoreId($storeId);
        $var->loadByCode($variable);
        return $var->getValue('html');
    }

}