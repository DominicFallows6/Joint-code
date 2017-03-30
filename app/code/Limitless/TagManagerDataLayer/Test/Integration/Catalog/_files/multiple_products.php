<?php

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;

createProducts();

function getProductList()
{
    return [
        1 => array('sku' => 'simple1', 'name' => 'Simple Product 1', 'price' => 10),
        2 => array('sku' => 'simple2', 'name' => 'Simple Product 2', 'price' => 20),
        3 => array('sku' => 'simple3', 'name' => 'Simple Product 3', 'price' => 5),
        4 => array('sku' => 'other4', 'name' => 'Other Name 4', 'price' => 12),
        5 => array('sku' => 'other5', 'name' => 'Other Name 5', 'price' => 5)
    ];
}

function createProducts()
{
    $products = getProductList();
    foreach ($products as $id => $product)
    {
        /** @var Product $productToSave */
        $productToSave = Bootstrap::getObjectManager()->create(Product::class);

        $productToSave
            ->setTypeId('simple')
            ->setId($id)
            ->setAttributeSetId(4)
            ->setWebsiteIds([1])
            ->setName($product['name'])
            ->setSku($product['sku'])
            ->setPrice($product['price'])
            ->setMetaTitle('meta title')
            ->setMetaKeyword('meta keyword')
            ->setMetaDescription('meta description')
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setStockData(['use_config_manage_stock' => 0])
            ->save();
    }
}