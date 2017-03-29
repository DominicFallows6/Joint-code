<?php

namespace Limitless\UpsellTab\Block;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Element\Template;

class View extends Template

{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * View constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    )
    {
        $this->coreRegistry = $context->getRegistry();
        parent::__construct($context);
    }

    public function getUpsellProducts() {
        $product = $this->coreRegistry->registry('product');
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