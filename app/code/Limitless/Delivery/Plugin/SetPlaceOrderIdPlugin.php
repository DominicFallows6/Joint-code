<?php

namespace Limitless\Delivery\Plugin;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class SetPlaceOrderIdPlugin
{
    public function __construct(AllocationFilterFactory $allocationFilterFactory)
    {
        $this->allocationFilterFactory = $allocationFilterFactory;
    }
    
    public function aroundPlaceOrder(
        CartManagementInterface $subject,
        \Closure $proceed,
        $cartID,
        PaymentInterface $payment = null
    ) {
        $orderId = $proceed($cartID, $payment);
        /** @var AllocationFilter $allocationFilterModel */
        $allocationFilterModel = $this->allocationFilterFactory->create();
        $allocationFilterModel->getResource()->load($allocationFilterModel, $cartID, AllocationFilter::QUOTE_ID);
        
        if ($allocationFilterModel->getId()) {
            $allocationFilterModel->setData(AllocationFilter::ORDER_ID, $orderId);
            $allocationFilterModel->getResource()->save($allocationFilterModel);
        }
        
        return $orderId;
    }
}