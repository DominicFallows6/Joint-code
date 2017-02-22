<?php

namespace Limitless\Delivery\Test\Integration;

use Limitless\Delivery\Model\AllocationFilter;
use Limitless\Delivery\Model\AllocationFilterFactory;
use Limitless\Delivery\Model\ResourceModel\AllocationFilter\Collection;
use Magento\TestFramework\ObjectManager;

/**
 * @magentoDbIsolation enabled
 */
class AllocationFilterOrmModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return AllocationFilter
     */
    private function instantiateModel()
    {
        return ObjectManager::getInstance()->create(AllocationFilter::class);
    }

    /**
     * @param $name
     * @return AllocationFilter
     */
    private function createAllocationFilter(string $name):AllocationFilter
    {
        $model = $this->instantiateModel();
        $model->setData(AllocationFilter::ALLOCATION_FILTER, $name);
        $model->setData(AllocationFilter::QUOTE_ID, '1');
        $model->setData(AllocationFilter::ORDER_ID, '2');
        $model->getResource()->save($model);
        return $model;
    }

    public function testSaveAndLoad()
    {
        $name = 'test';
        $model = $this->createAllocationFilter($name);
        $model2 = $this->instantiateModel();
        $model2->getResource()->load($model2, $model->getId());
        $this->assertSame(
            $model->getData(AllocationFilter::ALLOCATION_FILTER),
            $model2->getData(AllocationFilter::ALLOCATION_FILTER)
        );
    }

    public function testSaveAllocationFilter()
    {
        ObjectManager::getInstance()->create(AllocationFilterFactory::class);
    }

    public function testCollectionLoad()
    {
        $model1 = $this->createAllocationFilter('test-1');
        $model2 = $this->createAllocationFilter('test-2');
        $collection = ObjectManager::getInstance()->create(Collection::class);
        $items = $collection->getItems();
        $this->assertContains($model1->getId(), array_keys($items));
        $this->assertContains($model2->getId(), array_keys($items));
    }

}