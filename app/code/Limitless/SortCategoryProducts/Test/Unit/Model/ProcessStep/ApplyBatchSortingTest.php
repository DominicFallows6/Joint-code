<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ProcessStep;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class ApplyBatchSortingTest extends \PHPUnit_Framework_TestCase
{
    public function testAppliesSortBatchRowsToCategoryLinks()
    {
        $productAId = 1;
        $productASKU = 'foo';
        $productBId = 2;
        $productBSKU = 'bar';
        
        $mockCategoryA = $this->getMockBuilder(Category::class)->disableOriginalConstructor()->getMock();
        $mockCategoryA->method('getProductsPosition')->willReturn([$productAId => 5, $productBId => 6]);
        $mockCategoryA->expects($this->once())->method('setData')->with(
            'posted_products',
            [$productAId => 10, $productBId => 6]
        );
        $mockCategoryB = $this->getMockBuilder(Category::class)->disableOriginalConstructor()->getMock();
        $mockCategoryB->method('getProductsPosition')->willReturn([$productBId => 5, 3 => 6]);
        $mockCategoryB->expects($this->once())->method('setData')->with(
            'posted_products',
            [$productBId => 20, 3 => 6]
        );

        /** @var CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $stubCategoryRepository */
        $stubCategoryRepository = $this->getMock(CategoryRepositoryInterface::class);
        $stubCategoryRepository->method('get')->willReturnOnConsecutiveCalls($mockCategoryA, $mockCategoryB);

        /** @var ProductResource|\PHPUnit_Framework_MockObject_MockObject $stubProductResource */
        $stubProductResource = $this->getMockBuilder(ProductResource::class)->disableOriginalConstructor()->getMock();
        $stubProductResource->method('getProductsIdsBySkus')->willReturn(
            [$productASKU => $productAId, $productBSKU => $productBId]
        );
        
        /** @var CategoryResource|\PHPUnit_Framework_MockObject_MockObject $mockCategoryResource */
        $mockCategoryResource = $this->getMockBuilder(CategoryResource::class)->disableOriginalConstructor()->getMock();
        $mockCategoryResource->expects($this->exactly(2))->method('save')->withConsecutive(
            [$mockCategoryA],
            [$mockCategoryB]
        );

        $batchData = [
            [
                ApplyBatchSorting::CATEGORY_COLUMN => 1,
                ApplyBatchSorting::SKU_COLUMN      => $productASKU,
                ApplyBatchSorting::POSITION_COLUMN => 10,
            ],
            [
                ApplyBatchSorting::CATEGORY_COLUMN => 2,
                ApplyBatchSorting::SKU_COLUMN      => $productBSKU,
                ApplyBatchSorting::POSITION_COLUMN => 20,
            ],
        ];
        $applicator = new ApplyBatchSorting($stubCategoryRepository, $stubProductResource, $mockCategoryResource);
        $applicator->apply($batchData);
    }
}
