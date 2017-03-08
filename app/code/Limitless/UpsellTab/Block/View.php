<?php

namespace Limitless\UpsellTab\Block;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Element\Template;

class View extends Template

{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * View constructor.
     * @param Context $context
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
        $this->_coreRegistry = $context->getRegistry();
        parent::__construct($context);
    }

    public function getUpsellProducts() {
        $product = $this->_coreRegistry->registry('product');
        $upsellProductIds = $product->getUpsellProductIds();

        $tabHtml = "";

        if(!empty($upsellProductIds)) {
            $tabHtml =  $this->BuildUpSellsHtmlTab();
        }

        return $tabHtml;
    }

    public function BuildUpSellsHtmlTab() {

        $productItemTab = __('This Item');

        $productUpsellTab = __('You will Need');

        $tabs = '<div class="product-tabs"><ul><li class="active product-details"><a href="#">' . $productItemTab . '</a></li><li class="up-sells"><a href="#">' . $productUpsellTab .'</a></li></ul></div>';

        return $tabs;
    }
}