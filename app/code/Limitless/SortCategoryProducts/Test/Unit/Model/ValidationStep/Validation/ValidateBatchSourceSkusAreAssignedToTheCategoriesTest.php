<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceSkusAreAssignedToTheCategoriesTest extends \PHPUnit_Framework_TestCase
{
    private function createStubCategoryProductSkuList(array $categoryProductsSkuList): CategoryProductsSkuList
    {
        /** @var CategoryProductsSkuList $stubProductsSkuList */
        $stubProductsSkuList = new class($categoryProductsSkuList) extends CategoryProductsSkuList
        {
            private $testSkus;

            public function __construct(array $testSkus)
            {
                $this->testSkus = $testSkus;
            }

            public function getCategoryProductSkus($categoryId): array
            {
                return $this->testSkus;
            }
        };

        return $stubProductsSkuList;
    }

    private function createValidator(array $catgorySkuList = []): ValidateBatchSourceSkusAreAssignedToTheCategories
    {
        $categoryProductsSkuList = $this->createStubCategoryProductSkuList($catgorySkuList);

        return new ValidateBatchSourceSkusAreAssignedToTheCategories($categoryProductsSkuList);
    }

    public function testImplementsValidateBatchSourceInterface()
    {
        $this->assertInstanceOf(ValidateBatchSourceInterface::class, $this->createValidator());
    }

    public function testReturnsNoErrorsForEmptyArray()
    {
        $this->assertSame([], $this->createValidator()->getErrors([], []));
    }

    public function testReturnsErrorsForSkusThatAreNotAssignedToTheSpecifiedCategory()
    {
        $productSku = 'foo';
        
        $batchSource = [
            [42, $productSku, 10],
            [4711, $productSku, 20]
        ];
        
        $expected = [
            0 => ['The product with SKU "%1" is not assigned to category %2.', $productSku, 42],
            1 => ['The product with SKU "%1" is not assigned to category %2.', $productSku, 4711],
        ];

        $errors = $this->createValidator()->getErrors($batchSource, $batchSource);
        
        $this->assertSame($expected, $errors);
    }

    public function testReturnsOnlyErrorsForSkusThatAreNotAssigned()
    {
        $batchSource = [
            [42, 'foo', 10],
            [4711, 'bar', 20]
        ];
        
        $expected = [
            1 => ['The product with SKU "%1" is not assigned to category %2.', 'bar', 4711],
        ];
        
        $errors = $this->createValidator(['foo'])->getErrors($batchSource, $batchSource);
        
        $this->assertSame($expected, $errors);
    }
}
