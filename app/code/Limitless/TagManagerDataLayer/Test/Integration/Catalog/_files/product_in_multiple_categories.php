<?php

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

initCategoryLevelTwo(400, 'Category First', '1/2/3');
initCategoryLevelTwo(401, 'Category Second', '1/2/4');

initProductIntoCategories(333, 'Simple Product Three', 'simple333', 10, 18, array(400, 401));

function initCategoryLevelTwo($id, $name, $path)
{
    $category = Bootstrap::getObjectManager()->create(Category::class);
    $category->isObjectNew(true);
    $category->setId(
        $id
    )->setCreatedAt(
        '2014-06-23 09:50:07'
    )->setName(
        $name
    )->setParentId(
        2
    )->setPath(
        $path
    )->setLevel(
        2
    )->setAvailableSortBy(
        'name'
    )->setDefaultSortBy(
        'name'
    )->setIsActive(
        true
    )->setPosition(
        1
    )->setAvailableSortBy(
        ['position']
    )->save();
}

function initProductIntoCategories($id, $name, $sku, $price, $weight, $categories)
{
    /** @var $product \Magento\Catalog\Model\Product */
    $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
    $product->setTypeId(
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
    )->setId(
        $id
    )->setAttributeSetId(
        4
    )->setStoreId(
        1
    )->setWebsiteIds(
        [1]
    )->setName(
        $name
    )->setSku(
        $sku
    )->setPrice(
        $price
    )->setWeight(
        $weight
    )->setStockData(
        ['use_config_manage_stock' => 0]
    )->setCategoryIds(
        $categories
    )->setVisibility(
        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
    )->setStatus(
        \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
    )->save();
}