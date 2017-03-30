<?php

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/../../Catalog/_files/multiple_products.php';

initProductsWithQuantityInCart();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(\Magento\Checkout\Model\Session::class);

function getProductsWithQuantity()
{
    return [
        0 => array('name' => 'simple1', 'qty' => 3),
        1 => array('name' => 'simple2', 'qty' => 2),
        2 => array('name' => 'simple3', 'qty' => 2)
    ];
}

function initProductsWithQuantityInCart()
{
    $products = getProductsWithQuantity();

    /** @var ProductRepositoryInterface $productRepository */
    $productRepository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);

    /** @var $cart Cart */
    $cart = Bootstrap::getObjectManager()->create(Cart::class);

    foreach ($products as $productInfo)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $productRepository->get($productInfo['name']);

        $cart->addProduct(
            $product,
            new \Magento\Framework\DataObject(['qty' => $productInfo['qty']])
        );
    }

    $cart->save();
}



