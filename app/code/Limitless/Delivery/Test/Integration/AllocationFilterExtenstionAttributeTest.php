<?php

namespace Limitless\Delivery\Test\Integration;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;

class AllocationFilterExtenstionAttributeTest extends \PHPUnit_Framework_TestCase
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
     * @param $AllocationFilter
     * @param $orderId
     */
    protected function createAllocationFilterForId($allocationFilter, $orderId)
    {
        /** @var \Limitless\Delivery\Model\AllocationFilter $allocationFilter */
        $allocationFilterModel = ObjectManager::getInstance()->create(\Limitless\Delivery\Model\AllocationFilter::class);
        $allocationFilterModel->setData(\Limitless\Delivery\Model\AllocationFilter::ALLOCATION_FILTER, $allocationFilter);
        $allocationFilterModel->setData(\Limitless\Delivery\Model\AllocationFilter::ORDER_ID, $orderId);
        $allocationFilterModel->getResource()->save($allocationFilterModel);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderHasAllocationFilterExtensionAttribute()
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = ObjectManager::getInstance()->create(OrderRepositoryInterface::class);
        $orderId = $this->getOrderFixtureId();
        $allocationFilter = 'test-abc';
        $this->createAllocationFilterForId($allocationFilter, $orderId);
        $order = $orderRepository->get($orderId);
        $extensionAttributes = $order->getExtensionAttributes();
        $this->assertInstanceOf(\Magento\Sales\Api\Data\OrderExtensionInterface::class, $extensionAttributes);
        $this->assertSame($allocationFilter, $extensionAttributes->getAllocationFilter());
    }

    public function testOrdersHaveAllocationFilterExtensionAttributes()
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = ObjectManager::getInstance()->create(OrderRepositoryInterface::class);
        $orderId = $this->getOrderFixtureId();
        $allocationFilter = 'test-abc';
        $this->createAllocationFilterForId($allocationFilter, $orderId);
        $searchCriteria = ObjectManager::getInstance()->create(\Magento\Framework\Api\SearchCriteria::class);
        $searchResults = $orderRepository->getList($searchCriteria);
        $orders = $searchResults->getItems();
        foreach ($orders as $order) {
            $extensionAttributes = $order->getExtensionAttributes();
            $this->assertInstanceOf(\Magento\Sales\Api\Data\OrderExtensionInterface::class, $extensionAttributes);
            $this->assertSame($allocationFilter, $extensionAttributes->getAllocationFilter());
        }
    }
}