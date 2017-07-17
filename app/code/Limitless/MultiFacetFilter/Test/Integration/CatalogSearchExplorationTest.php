<?php

namespace Limitless\MultiFacetFilter\Test\Integration;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search;
use Magento\TestFramework\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @magentoDbIsolation enabled
 */
class CatalogSearchExplorationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->setAttributeFilterableInSearch('style_bags');
        $this->setAttributeFilterableInSearch('strap_bags');
        $this->setAttributeFilterableInSearch('material');
    }

    private function setAttributeFilterableInSearch($code)
    {
        /** @var EavConfig $eavConfig */
        $eavConfig = $this->objectManager->get(EavConfig::class);
        $attribute = $eavConfig->getAttribute(Product::ENTITY, $code);
        $attribute->setData('is_filterable_in_search', 1);
        $attribute->getResource()->save($attribute);
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
    public function testToExploreSearchRequestBuilder()
    {
        /** @var ScopeResolverInterface $scopeResolver */
        $scopeResolver = $this->objectManager->get(ScopeResolverInterface::class);

        /** @var Search\SearchEngineInterface $searchEngine */
        $searchEngine = $this->objectManager->create(Search\SearchEngineInterface::class);

        /** @var Search\Request\Builder $builder */
        $builder = $this->objectManager->create(Search\Request\Builder::class);
        $builder->setRequestName('quick_search_container');
        $builder->bindDimension('scope', $scopeResolver->getScope()->getId());
        $builder->bind('style_bags', ['35', '37']);
        $builder->setFrom(0);
        $builder->setSize(1000);
        $request = $builder->create();
        $searchResponse = $searchEngine->search($request);
        $this->assertGreaterThan(0, $searchResponse->count());
        /*
         * Exploration result: It works!
         */
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
    public function testSearchCriteriaCanHaveFilterWithArrayValues()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $filter = new \Magento\Framework\Api\Filter();
        $filter->setField('style_bags');
        $filter->setValue([0 => '35', 1 => '37']);
        $searchCriteriaBuilder->addFilter($filter);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setRequestName('quick_search_container');

        /** @var SearchInterface $searchAdapter */

        $searchAdapter = $this->objectManager->create(SearchInterface::class);
        $result = $searchAdapter->search($searchCriteria);

        $this->assertSame(8, $result->getTotalCount());
        /**
         * Exploration result: no problem!
         */
    }

    public function testFulltextSearchProductCollectionAppliesFiltersWithArrayValues()
    {
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
        $collection = ObjectManager::getInstance()->create(
            \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::class
        );

        $searchCriteriaBuilderMock = $this->getMockBuilder(
            \Magento\Framework\Api\Search\SearchCriteriaBuilder::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $searchCriteriaBuilderMock->expects($this->once())->method('addFilter');
        $collection->setSearchCriteriaBuilder($searchCriteriaBuilderMock);

        $collection->addFieldToFilter('material', ['45', '46']);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testAttributeSearchFilterCanProcessArraySearchTerms()
    {
        $mockProductCollection = $this
            ->getMockBuilder(\Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockState = $this->getMockBuilder(\Magento\Catalog\Model\Layer\State::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockLayer = $this->getMockBuilder(\Magento\Catalog\Model\Layer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockLayer->method('getState')->willReturn($mockState);
        $mockLayer->method('getProductCollection')->willReturn($mockProductCollection);

        /** @var \Magento\CatalogSearch\Model\Layer\Filter\Attribute $attributeFilter */
        $attributeFilter = ObjectManager::getInstance()->create(
            \Magento\CatalogSearch\Model\Layer\Filter\Attribute::class,
            ['layer' => $mockLayer]
        );

        /** @var EavConfig $eavConfig */
        $eavConfig = $this->objectManager->get(EavConfig::class);
        $attribute = $eavConfig->getAttribute(Product::ENTITY, 'material');
        $attributeFilter->setAttributeModel($attribute);

        /** @var RequestInterface|MockObject $mockRequest */
        $mockRequest = $this->getMock(RequestInterface::class);
        $mockRequest->method('getParam')->willReturn(['45', '46']);

        $mockState->expects($this->exactly(2))->method('addFilter');

        $attributeFilter->apply($mockRequest);
    }
    
    /*
     * 1. FIXED: \Magento\CatalogSearch\Model\Layer\Filter\Attribute::apply()
     *    Assumes the filter value is a single non-array value and
     *    uses it with $label = $this->getOptionText($value);
     *    Solutions: if the value is an array, add one filter state item per value
          if (! is_array($attributeValue)) {
              $attributeValue = [$attributeValue];
          }

          foreach ($attributeValue as $value) {
              $label = $this->getOptionText($value);
              $this->getLayer()
                  ->getState()
                  ->addFilter($this->_createItem($label, $value));
          }
     
     *
     * 2. FIXED: \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection::addFieldToFilter()
     *    The following condition is not type safe
     *    !in_array(key($condition), ['from', 'to'])
     *    If the array key is 0 (integer value) it matches 'from' because of PHP type juggling
     *    Fix: set the type safe flag argument for in_array()
     *    !in_array(key($condition), ['from', 'to'], true)
     * 
     * Note: Trying to work around number 2. above by using string array keys (e.g. '0') works
     *    in that instance, but the ElasticSearch extension is not able to parse Elastic responses
     *    with string keys, it expects a proper JSON integer indexed array.
     * 
     * 
     * 3: By fixing 2. from above, a regression became apparent.
     * "The core" adds a visibility filter with an array value on the fulltext collection.
     * The visibility must be [3 or 4].
     * Because of 2. before our fix the filter wasn't applied for the elastic search query.
     * Now, with the fix, the visibility filter is applied, but the visibility attribute is
     * not a query field in the elastic index.
     * Possible solutions:
     * Either not apply the visibility filter to the catalog fulltext collection (or the search engine query)
     * Or make the visibility field in elastic queryable.
     */
}
