<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;

class ValidateBatchSourceSkusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductSearchResults;
    
    /**
     * @return SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        $stubSearchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubSearchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stubSearchCriteriaBuilder->method('create')->willReturn($stubSearchCriteria);

        return $stubSearchCriteriaBuilder;
    }

    /**
     * @param string $sku
     * @return ProductInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createProductWithSku(string $sku): ProductInterface
    {
        $stubProduct = $this->getMock(ProductInterface::class);
        $stubProduct->method('getSku')->willReturn($sku);
        
        return $stubProduct;
    }

    private function createValidator(): ValidateBatchSourceSkus
    {
        $stubSearchCriteriaBuilder = $this->createStubSearchCriteriaBuilder();
        
        /** @var ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject $stubProductRepository */
        $stubProductRepository = $this->getMock(ProductRepositoryInterface::class);
        $stubProductRepository->method('getList')->willReturn($this->stubProductSearchResults);
        
        return new ValidateBatchSourceSkus($stubProductRepository, $stubSearchCriteriaBuilder);
    }

    protected function setUp()
    {
        $this->stubProductSearchResults = $this->getMock(ProductSearchResultsInterface::class);
    }

    public function testImplementsValidatesBatchSourceValidatorInterface()
    {
        $this->assertInstanceOf(ValidateBatchSourceInterface::class, $this->createValidator());
    }

    public function testReturnsNoErrorsForAnEmptyList()
    {
        $this->assertSame([], $this->createValidator()->getErrors([], []));
    }

    public function testReturnsErrorsForListOfInvalidSkus()
    {
        $expected = [
            0 => ['No product with SKU "%1" exists.', 'foo'],
            1 => ['No product with SKU "%1" exists.', 'bar'],
            2 => ['No product with SKU "%1" exists.', 'baz'],
        ];
        $batchSource = [
            [1, 'foo', 10],
            [1, 'bar', 20],
            [1, 'baz', 30]
        ];
        $this->assertSame($expected, $this->createValidator()->getErrors($batchSource, $batchSource));
    }

    public function testReturnsNoErrorsForListOfValidSkus()
    {
        $products = [
            $this->createProductWithSku('foo'),
            $this->createProductWithSku('bar'),
            $this->createProductWithSku('baz'),
        ];
        $batchSource = [
            [1, 'foo', 10],
            [1, 'bar', 20],
            [1, 'baz', 30]
        ];
        $this->stubProductSearchResults->method('getItems')->willReturn($products);
        $this->assertSame([], $this->createValidator()->getErrors($batchSource, $batchSource));
    }
}
