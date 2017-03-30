<?php

use Magento\Framework\Exception\NoSuchEntityException;

\Magento\TestFramework\Helper\Bootstrap::getInstance()->getInstance()->reinitialize();

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('simple1', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {

}

try {
    $product2 = $productRepository->get('simple2', false, null, true);
    $productRepository->delete($product2);
} catch (NoSuchEntityException $e) {

}

try {
    $product3 = $productRepository->get('simple3', false, null, true);
    $productRepository->delete($product3);
} catch (NoSuchEntityException $e) {

}

try {
    $product4 = $productRepository->get('other4', false, null, true);
    $productRepository->delete($product4);
} catch (NoSuchEntityException $e) {

}

try {
    $product5 = $productRepository->get('other5', false, null, true);
    $productRepository->delete($product5);
} catch (NoSuchEntityException $e) {

}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
