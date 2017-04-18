<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;

class CategoryProductsSkuListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CategoryLinkManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCategoryLinkManagement;

    private function createCategoryProductsSkuList(): CategoryProductsSkuList
    {
        return new CategoryProductsSkuList($this->stubCategoryLinkManagement);
    }

    private function createStubAssignedProduct(string $sku): CategoryProductLinkInterface
    {
        /** @var CategoryProductLinkInterface|\PHPUnit_Framework_MockObject_MockObject $stubCategoryProductLink */
        $stubCategoryProductLink = $this->getMock(CategoryProductLinkInterface::class);
        $stubCategoryProductLink->method('getSku')->willReturn($sku);
        return $stubCategoryProductLink;
    }

    protected function setUp()
    {
        $this->stubCategoryLinkManagement = $this->getMock(CategoryLinkManagementInterface::class);
    }
    
    public function testReturnsAnEmptyArrayIfCategoryDoesNotExist()
    {
        $categoryId = 42;
        $this->assertSame([], $this->createCategoryProductsSkuList()->getCategoryProductSkus($categoryId));
    }

    public function testReturnsAnArrayWithCategoryProductSkus()
    {
        $this->stubCategoryLinkManagement->method('getAssignedProducts')->willReturn([
            $this->createStubAssignedProduct('foo'),
            $this->createStubAssignedProduct('bar'),
        ]);
        $categoryId = 1;
        $this->assertSame(['foo', 'bar'], $this->createCategoryProductsSkuList()->getCategoryProductSkus($categoryId));
    }
}
