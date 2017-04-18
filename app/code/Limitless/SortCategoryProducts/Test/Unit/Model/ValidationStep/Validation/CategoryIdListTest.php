<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CategoryIdListTest extends \PHPUnit_Framework_TestCase
{
    private function createStubCategoryCollectionFactory($stubCategoryCollection): CategoryCollectionFactory
    {
        /** @var CategoryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject $stubCategoryCollectionFactory */
        $stubCategoryCollectionFactory = $this->getMockBuilder(CategoryCollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $stubCategoryCollectionFactory->method('create')->willReturn($stubCategoryCollection);
        return $stubCategoryCollectionFactory;
    }

    public function testReturnsListOfAllCategoryIds()
    {
        /** @var CategoryCollection|\PHPUnit_Framework_MockObject_MockObject $stubCategoryCollection */
        $stubCategoryCollection = $this->getMockBuilder(CategoryCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubCategoryCollection->method('getAllIds')->willReturn([1, 2, 3, 4, 5]);
        
        $stubCategoryCollectionFactory = $this->createStubCategoryCollectionFactory($stubCategoryCollection);

        $this->assertSame([1, 2, 3, 4, 5], (new CategoryIdList($stubCategoryCollectionFactory))->getAllCategoryIds());
    }
}
