<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ProcessStep;

use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory;
use Magento\Catalog\Model\Category;

class ApplyBatchSortingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return CategoryProductLinkInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createFakeCategoryProductLinkFactory(): \PHPUnit_Framework_MockObject_MockObject
    {
        $fakeCategoryLinkFactory = $this->getMockBuilder(CategoryProductLinkInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $fakeCategoryLinkFactory->expects($this->atLeastOnce())->method('create')->willReturnCallback(function () {
            $mockCategoryLink = $this->getMock(CategoryProductLinkInterface::class);
            $mockCategoryLink->expects($this->once())->method('setCategoryId');
            $mockCategoryLink->expects($this->once())->method('setSku');
            $mockCategoryLink->expects($this->once())->method('setPosition');

            return $mockCategoryLink;
        });

        return $fakeCategoryLinkFactory;
    }

    public function testAppliesSortBatchRowsToCategoryLinks()
    {
        $fakeCategoryLinkFactory = $this->createFakeCategoryProductLinkFactory();

        /** @var CategoryLinkRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $mockCategoryLinkRepository */
        $mockCategoryLinkRepository = $this->getMock(CategoryLinkRepositoryInterface::class);
        $mockCategoryLinkRepository->expects($this->atLeastOnce())->method('save');
        
        /** @var CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $stubCategoryRepository */
        $stubCategoryRepository = $this->getMock(CategoryRepositoryInterface::class);
        $stubCategoryRepository->method('get')->willReturn(
            $this->getMockBuilder(Category::class)->disableOriginalConstructor()->getMock()
        );

        $applicator = new ApplyBatchSorting(
            $fakeCategoryLinkFactory,
            $mockCategoryLinkRepository,
            $stubCategoryRepository
        );
        $batchData = [[1, 'foo', 10]];
        $applicator->apply($batchData);
    }
}
