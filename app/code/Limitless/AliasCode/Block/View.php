<?php

namespace Limitless\AliasCode\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function getAliasCode() {
        $product = $this->getCurrentProduct();
        if($product && $product->getCustomAttribute('alias')) {
            $aliasCodeAttribute = $product->getData('alias');
        } else {
            $aliasCodeAttribute = "";
        }
        return $aliasCodeAttribute;
    }

    /**
     * @return Product|null
     */
    private function getCurrentProduct()
    {
        return $this->registry->registry('product');
    }

}