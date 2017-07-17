<?php


namespace Limitless\Elastic\Test\Integration;

use Limitless\Elastic\Adapter\SearchAdapter;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\TierPriceInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as ProductFulltextCollection;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\HTTP\ClientFactory as HttpClientFactory;
use Magento\Framework\Search;
use Magento\Framework\Search\Request\Builder as SearchRequestBuilder;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Indexer\Model\Indexer\CollectionFactory as IndexerCollectionFactory;
use Magento\TestFramework\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @magentoDbIsolation enabled
 */
class ElasticSearchEdgeToEdgeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function validateElasticsearchInstanceIsRunning()
    {
        /** @var HttpClientFactory $httpClientFactory */
        $httpClientFactory = $this->objectManager->get(HttpClientFactory::class);
        $httpClient = $httpClientFactory->create();

        $uri = 'http://localhost:9200/';

        try {
            $httpClient->get($uri);

            if ($httpClient->getStatus() !== 200) {
                $this->markTestSkipped('ElasticSearch Not Accessible on ' . $uri);
            }
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    private function setAttributeFilterableInSearch(string $code)
    {
        /** @var EavConfig $eavConfig */
        $eavConfig = $this->objectManager->get(EavConfig::class);
        $attribute = $eavConfig->getAttribute(Product::ENTITY, $code);
        $attribute->setData('is_filterable_in_search', 1);
        $attribute->getResource()->save($attribute);
    }

    private function getDefaultAttributeSetId(): int
    {
        /** @var EavConfig $eavConfig */
        $eavConfig = $this->objectManager->create(EavConfig::class);
        return (int) $eavConfig->getEntityType('catalog_product')->getDefaultAttributeSetId();
    }

    private function saveProduct(ProductInterface $product): ProductInterface
    {
        /** @var ProductRepositoryInterface $productRepo */
        $productRepo = $this->objectManager->create(ProductRepositoryInterface::class);
        $savedProduct = $productRepo->save($product);
        $this->addToSearchEngineIndex($savedProduct);
        return $savedProduct;
    }

    private function addToSearchEngineIndex(ProductInterface $savedProduct)
    {
        /** @var IndexerCollectionFactory $indexerCollectionFactory */
        $indexerCollectionFactory = $this->objectManager->create(IndexerCollectionFactory::class);
        /** @var \Magento\Indexer\Model\Indexer $indexer */
        $indexerCollection = $indexerCollectionFactory->create();

        $indexer = $indexerCollection->getItemByColumnValue('indexer_id', 'catalog_product_price');
        $indexer->reindexList([$savedProduct->getId()]);

        $indexer = $indexerCollection->getItemByColumnValue('indexer_id', 'catalogsearch_fulltext');
        $indexer->reindexList([$savedProduct->getId()]);
    }

    private function createProduct(string $description): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->objectManager->create(ProductInterface::class);

        $product->setSku(uniqid('test-'));
        $product->setName(uniqid('test product'));
        $product->setCustomAttribute('description', $description);
        $product->setPrice(10.00);
        $product->setAttributeSetId($this->getDefaultAttributeSetId());
        $this->setProductStockQty($product, 100);

        return $this->saveProduct($product);
    }

    private function setProductStockQty(ProductInterface $product, int $qty)
    {
        $stockItem = $this->createStockItem($qty);
        $extensionAttributes = $this->createProductExtensionAttributes();
        $extensionAttributes->setStockItem($stockItem);
        $product->setExtensionAttributes($extensionAttributes);
    }

    private function createStockItem(int $qty): StockItemInterface
    {
        /** @var StockItemInterface $stockItem */
        $stockItem = $this->objectManager->create(StockItemInterface::class);
        $stockItem->setQty($qty);
        $stockItem->setIsInStock(true);
        $stockItem->setManageStock(true);
        $stockItem->setBackorders(true);
        $stockItem->setUseConfigManageStock(true);
        return $stockItem;
    }

    private function createProductExtensionAttributes(): ProductExtensionInterface
    {
        /** @var ProductExtensionFactory $extensionAttributesFactory */
        $extensionAttributesFactory = $this->objectManager->create(ProductExtensionFactory::class);
        return $extensionAttributesFactory->create();
    }


    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->validateElasticsearchInstanceIsRunning();
    }

    /**
     * @magentoArea frontend
     * @magentoCache config disabled
     * @magentoConfigFixture current_store catalog/search/engine elasticsearch
     * @magentoConfigFixture current_store catalog/search/elasticsearch_server_hostname localhost
     * @magentoConfigFixture current_store catalog/search/elasticsearch_server_port 9200
     * @magentoConfigFixture current_store catalog/search/elasticsearch_index_prefix magento2
     * @magentoConfigFixture current_store catalog/search/elasticsearch_server_timeout 15
     */
    public function testSimpleSearch()
    {
        //$product = $this->createProduct('foo description');

        /** @var ProductFulltextCollection $searchCollection */
        $searchCollection = $this->objectManager->create(ProductFulltextCollection::class);
        $searchCollection->addSearchFilter('jacket');

        $items = $searchCollection->getItems();
        $this->assertNotEmpty($items);
    }
}