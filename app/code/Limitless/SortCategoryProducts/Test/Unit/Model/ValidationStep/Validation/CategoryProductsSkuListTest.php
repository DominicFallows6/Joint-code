<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class CategoryProductsSkuListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductResource;

    private function createCategoryProductsSkuList(): CategoryProductsSkuList
    {
        return new CategoryProductsSkuList($this->stubProductResource);
    }

    protected function setUp()
    {
        $this->stubProductResource = $this->getMockBuilder(ProductResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubConnection = new class() {
            public function __call($method, $args) { return $this; }

            public function fetchCol() { return []; }
        };
        $this->stubProductResource->method('getConnection')->willReturn($stubConnection);
    }
    
    public function testReturnsAnEmptyArrayIfCategoryDoesNotExist()
    {
        $categoryId = 42;
        $this->stubProductResource->method('getProductsSku')->willReturn([]);
        $this->assertSame([], $this->createCategoryProductsSkuList()->getCategoryProductSkus($categoryId));
    }

    public function testReturnsAnArrayWithCategoryProductSkus()
    {
        $this->stubProductResource->method('getProductsSku')->willReturn([
            ['entity_id' => 111, 'sku' => 'foo'],
            ['entity_id' => 222, 'sku' => 'bar'],
        ]);
        $categoryId = 1;
        $this->assertSame(['foo', 'bar'], $this->createCategoryProductsSkuList()->getCategoryProductSkus($categoryId));
    }
}
