<?php

namespace Limitless\DeliveryPriority\Test;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;

class DeliveryPriorityExtensionAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return mixed
     */
    protected function getOrderFixtureId()
    {
        /** @var \Magento\Sales\Model\Order $orderFixture */
        $orderFixture = ObjectManager::getInstance()->create(\Magento\Sales\Model\Order::class);
        $orderFixture->loadByIncrementId('100000001');
        $id = $orderFixture->getId();
        return $id;
    }

    /**
     * @param $deliveryPriority
     * @param $orderId
     */
    private function createDeliveryPriorityForId($deliveryPriority, $orderId)
    {
        /** @var \Limitless\DeliveryPriority\Model\Priority $deliveryPriorityModel */
        $deliveryPriorityModel = ObjectManager::getInstance()->create(\Limitless\DeliveryPriority\Model\Priority::class);
        $deliveryPriorityModel->setData(\Limitless\DeliveryPriority\Model\Priority::DELIVERY_PRIORITY, $deliveryPriority);
        $deliveryPriorityModel->setData(\Limitless\DeliveryPriority\Model\Priority::ORDER_ID, $orderId);
        $deliveryPriorityModel->getResource()->save($deliveryPriorityModel);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderHasDeliveryPriorityExtensionAttribute()
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = ObjectManager::getInstance()->create(OrderRepositoryInterface::class);
        $orderId = $this->getOrderFixtureId();
        $deliveryPriority = 'a';
        $this->createDeliveryPriorityForId($deliveryPriority, $orderId);
        $order = $orderRepository->get($orderId);
        $extensionAttributes = $order->getExtensionAttributes();
        $this->assertInstanceOf(\Magento\Sales\Api\Data\OrderExtensionInterface::class, $extensionAttributes);
        $this->assertSame($deliveryPriority, $extensionAttributes->getPriority());
    }

}