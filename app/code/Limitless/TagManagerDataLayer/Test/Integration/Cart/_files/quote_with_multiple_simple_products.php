<?php

//Todo fix this require:
require __DIR__ . '/../../../../../../../../dev/tests/integration/testsuite/Magento/Catalog/_files/products.php';

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

/** @var $product \Magento\Catalog\Model\Product */
$product = $productRepository->get('simple');

$requestInfo = new \Magento\Framework\DataObject(['qty' => 3]);

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Checkout\Model\Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(\Magento\Checkout\Model\Session::class);