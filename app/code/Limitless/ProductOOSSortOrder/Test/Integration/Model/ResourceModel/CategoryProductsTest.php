<?php

namespace Limitless\ProductOOSSortOrder\Test\Integration\Model\ResourceModel;

use Limitless\ProductOOSSortOrder\Model\ResourceModel\CategoryProducts;
use Magento\TestFramework\ObjectManager;

class CategoryProductsTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsEmptyArrayIfNoMatch()
    {
        /** @var CategoryProducts $categoryProducts */
        $categoryProducts = ObjectManager::getInstance()->create(CategoryProducts::class);
        $result = $categoryProducts->getCategoryIdsForProductIds([-1]);

        $this->assertEmpty($result);
    }

    public function testReturnsEmptyArrayIfNoProductsIdsGiven()
    {
        /** @var CategoryProducts $categoryProducts */
        $categoryProducts = ObjectManager::getInstance()->create(CategoryProducts::class);
        $result = $categoryProducts->getCategoryIdsForProductIds([]);

        $this->assertEmpty($result);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testReturnsCategoriesForGivenProductIds()
    {
        $productIds = ['333'];
        $expectedCategoryIds = ['333'];

        /** @var CategoryProducts $categoryProducts */
        $categoryProducts = ObjectManager::getInstance()->create(CategoryProducts::class);
        $result = $categoryProducts->getCategoryIdsForProductIds($productIds);

        $this->assertSame($expectedCategoryIds, $result);
    }
}