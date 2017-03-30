<?php

namespace Limitless\TagManagerDataLayer\Block\DataLayer;

use Limitless\TagManagerDataLayer\Api\DataLayerAbstract;
use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Helper\Cart;
use Magento\Catalog\Model\ProductFactory;
use Limitless\TagManagerDataLayer\Helper\TagsDataLayer\DynamicRemarketing;

class CartDataLayer extends DataLayerAbstract
{
    /** @var array */
    private $dataLayerVariables;

    /** @var Cart */
    private $cart;

    /** @var ProductFactory */
    private $productFactory;

    /** @var DynamicRemarketing */
    private $dynamicRemarketingHelper;

    public function __construct(
        Context $context,
        Cart $cart,
        ProductFactory $productFactory,
        DynamicRemarketing $dynamicRemarketingHelper,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->cart = $cart;
        $this->productFactory = $productFactory;
        $this->dynamicRemarketingHelper = $dynamicRemarketingHelper;
        $this->dataLayerVariables = [];
    }

    public function initDataLayerVariables()
    {
        $this->initDynamicRemarketingDLVariables();
    }

    /**
     * @return array
     */
    public function getDataLayerVariables(): array
    {
        return $this->dataLayerVariables;
    }

    private function initDynamicRemarketingDLVariables()
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $cartItems */
        $cartItems = $this->cart->getCart()->getItems();

        $products = $quantities = [];

        /** @var \Magento\Quote\Model\Quote\Item $cartItem */
        foreach ($cartItems as $cartItem) {
            $products[] = $cartItem->getProduct();
            $quantities[] = $cartItem->getQty();
        }

        $this->dynamicRemarketingHelper->buildAllDynamicRemarketingValues(
            'cart',
            $products,
            '',
            $this->getDRTotalValue(),
            $quantities
        );

        $this->mergeIntoDataLayer($this->dynamicRemarketingHelper->getAllDynamicRemarketingValuesInArray());
    }

    /**
     * @param array $mergeRequest
     */
    private function mergeIntoDataLayer($mergeRequest)
    {
        $this->dataLayerVariables = array_merge($mergeRequest, $this->dataLayerVariables);
    }

    private function getDRTotalValue()
    {
        $vatSetting = $this->dynamicRemarketingHelper->getTotalVatSetting();
        $cartTotal = $this->cart->getQuote()->getGrandTotal(); //inclusive of Tax

        switch ($vatSetting) {
            case 'exclude':
                $cartTotal = $this->cart->getQuote()->getSubtotal();
                break;
        }
        return number_format($cartTotal, 2);
    }
}