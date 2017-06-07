<?php

declare(strict_types=1);

namespace Limitless\ProductApiPerformance\Test\Integration;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\TestFramework\ObjectManager;


/**
 * @magentoDataFixture Magento/Catalog/_files/products_related_multiple.php
 * @magentoAppIsolation enabled
 */
class ProductLinkSaveHandlerTest extends \PHPUnit_Framework_TestCase
{
    private function createProduct(): ProductInterface
    {
        return ObjectManager::getInstance()->create(ProductInterface::class);
    }

    private function createProductRepository(): ProductRepositoryInterface
    {
        return ObjectManager::getInstance()->create(ProductRepositoryInterface::class);
    }

    private function createProductLinkInstance(): ProductLinkInterface
    {
        return ObjectManager::getInstance()->create(ProductLinkInterface::class);
    }

    private function getProductInstanceBasedOnFixture(int $id, string $sku): ProductInterface
    {
        $product = $this->createProduct();
        $product->setId($id);
        $product->setSku($sku);
        $product->setName('Simple Product With Related Product');
        $product->setAttributeSetId(4);
        $product->setTypeId(Type::TYPE_SIMPLE);

        return $product;
    }

    private function getFixtureLink1(): ProductLinkInterface
    {
        return $this->createProductLink('simple_with_cross', 'simple');
    }

    private function getFixtureLink2(): ProductLinkInterface
    {
        return $this->createProductLink('simple_with_cross', 'simple_with_cross_two');
    }

    private function createProductLink(string $sku, string $linkedSku): ProductLinkInterface
    {
        $productLink = $this->createProductLinkInstance();
        $productLink->setSku($sku);
        $productLink->setLinkedProductSku($linkedSku);
        $productLink->setPosition(1);
        $productLink->setLinkType('related');
        
        return $productLink;
    }

    private function assertProductHasLink(ProductLinkInterface $link, ProductInterface $product)
    {
        $productLinks = (array) $product->getProductLinks();
        foreach ($productLinks as $productLink) {
            if ($this->isSameLink($link, $productLink)) {
                $this->assertSame(1, 1);
                return;
            }
        }
        $this->fail(sprintf(
            'Product "%s" did not contain the expected product link to "%s"',
            $product->getSku(),
            $link->getLinkedProductSku()
        ));
    }

    private function isSameLink(ProductLinkInterface $linkA, ProductLinkInterface $linkB): bool
    {
        return $linkA->getSku() === $linkB->getSku() &&
               $linkA->getLinkType() === $linkB->getLinkType() &&
               $linkA->getLinkedProductSku() === $linkB->getLinkedProductSku() &&
               (int) $linkA->getPosition() === (int) $linkB->getPosition();
    }

    public function testSavingProductWithoutSpecifyingLinksDoesNotChangeLinkingData()
    {
        $product = $this->getProductInstanceBasedOnFixture(2, 'simple_with_cross');
        $this->assertEmpty($product->getProductLinks());

        $savedProduct = $this->createProductRepository()->save($product);

        $this->assertProductHasLink($this->getFixtureLink1(), $savedProduct);
        $this->assertProductHasLink($this->getFixtureLink2(), $savedProduct);
    }

    public function testSavingProductWithExistingLinksDoesNotChangeLinkingData()
    {
        $fixtureProduct = $this->createProductRepository()->get('simple_with_cross');
        $this->assertCount(2, $fixtureProduct->getProductLinks());
        
        $savedProduct = $this->createProductRepository()->save($fixtureProduct);

        $this->assertProductHasLink($this->getFixtureLink1(), $savedProduct);
        $this->assertProductHasLink($this->getFixtureLink2(), $savedProduct);
    }

    public function testSavingProductWithChangedLinksUpdatesLinkingData()
    {
        $link1 = $this->getFixtureLink1();
        $link2 = $this->getFixtureLink2();
        $link2->setPosition(100);
        
        $product = $this->getProductInstanceBasedOnFixture(2, 'simple_with_cross');
        $product->setProductLinks([$link1, $link2]);
        
        $this->createProductRepository()->save($product);

        $loadedProduct = $this->createProductRepository()->get('simple_with_cross');
        $this->assertProductHasLink($link1, $loadedProduct);
        $this->assertProductHasLink($link2, $loadedProduct);
    }

    public function testSavingProductWithRemovedLinkUpdatesLinkingData()
    {
        $product = $this->getProductInstanceBasedOnFixture(2, 'simple_with_cross');
        $link = $this->getFixtureLink1();
        $product->setProductLinks([$link]);
        
        $this->createProductRepository()->save($product);

        $loadedProduct = $this->createProductRepository()->get('simple_with_cross');
        $this->assertProductHasLink($link, $loadedProduct);
        $this->assertCount(1, $loadedProduct->getProductLinks(), 'Product link count does not match expected value');
    }

    public function testSavingProductWithNewLinkUpdatesLinkingData()
    {
        $productFixtureWithoutLinks = $this->getProductInstanceBasedOnFixture(1, 'simple');
        
        $link = $this->createProductLink('simple', 'simple_with_cross_two');
        $productFixtureWithoutLinks->setProductLinks([$link]);
        
        $this->createProductRepository()->save($productFixtureWithoutLinks);

        $loadedProduct = $this->createProductRepository()->get('simple');
        $this->assertProductHasLink($link, $loadedProduct);
        $this->assertCount(1, $loadedProduct->getProductLinks(), 'Product link count does not match expected value');
    }
}
