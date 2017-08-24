<?php

namespace Limitless\Delivery\Plugin;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

class SetPlaceOrderIdPlugin
{
    /**
     * @var AllocationFilterFactory
     */
    private $allocationFilterFactory;

    public function __construct(AllocationFilterFactory $allocationFilterFactory)
    {
        $this->allocationFilterFactory = $allocationFilterFactory;
    }

    public function aroundPlace (
        OrderManagementInterface $subject,
        \Closure $proceed,
        OrderInterface $order
    ) {
        /** @var OrderInterface $placedOrder */
        $placedOrder = $proceed($order);

        /** @var AllocationFilter $allocationFilterModel */
        $allocationFilterModel = $this->allocationFilterFactory->create();
        $allocationFilterModel->getResource()->load($allocationFilterModel, $placedOrder->getQuoteId(), AllocationFilter::QUOTE_ID);

        if ($allocationFilterModel->getId()) {
            $allocationFilterModel->setData(AllocationFilter::ORDER_ID, $placedOrder->getEntityId());
            $allocationFilterModel->getResource()->save($allocationFilterModel);
        }

        return $placedOrder;
    }
}