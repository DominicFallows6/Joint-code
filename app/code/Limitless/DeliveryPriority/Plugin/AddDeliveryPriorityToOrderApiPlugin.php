<?php

namespace Limitless\DeliveryPriority\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Limitless\DeliveryPriority\Model\Priority;
use Limitless\DeliveryPriority\Model\PriorityFactory;

class AddDeliveryPriorityToOrderApiPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionAttributesFactory;

    /**
     * @var PriorityFactory
     */
    private $priorityFactory;

    /**
     * AddDeliveryPriorityToOrderApiPlugin constructor.
     * @param OrderExtensionFactory $orderExtensionAttributesFactory
     * @param PriorityFactory $priorityFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionAttributesFactory,
        PriorityFactory $priorityFactory
    ) {
        $this->orderExtensionAttributesFactory = $orderExtensionAttributesFactory;
        $this->priorityFactory = $priorityFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param \Closure $proceed
     * @param $orderId
     * @return OrderInterface
     */
    public function aroundGet(OrderRepositoryInterface $subject, \Closure $proceed, $orderId)
    {
        /** @var OrderInterface $order */
        $order = $proceed($orderId);
        $this->addExtensionAttributesToOrder($order);
        return $order;
    }

    /**
     * @param OrderInterface $order
     */
    private function addExtensionAttributesToOrder(OrderInterface $order)
    {
        /** @var Priority $priority */
        $priority = $this->priorityFactory->create();
        $priority->getResource()->load($priority, $order->getEntityId(), Priority::ORDER_ID);

        if ($priority->getId()) {
            $extensionAttributes = $order->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->orderExtensionAttributesFactory->create();
                $order->setExtensionAttributes($extensionAttributes);
            }

            $priorityString = $priority->getData(Priority::DELIVERY_PRIORITY);
            $extensionAttributes->setPriority($priorityString);
        }
    }
}