<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceSkusAreAssignedToTheCategories implements ValidateBatchSourceInterface
{
    /**
     * @var CategoryProductsSkuList
     */
    private $categoryProductsSkuList;

    public function __construct(CategoryProductsSkuList $categoryProductsSkuList)
    {
        $this->categoryProductsSkuList = $categoryProductsSkuList;
    }

    public function getErrors(array $origBatchSource, array $validBatchSource): array
    {
        $errors = $this->getSkusNotAssignedToCategory($validBatchSource);

        return array_map(function ($row): array {
            return [
                'The product with SKU "%1" is not assigned to category %2.',
                $row[self::SKU_COLUMN],
                $row[self::CATEGORY_COLUMN]
            ];
        }, $errors);
    }

    public function getSkusNotAssignedToCategory(array $rows): array
    {
        $nonAssignedSkus = [];
        foreach ($rows as $idx => $row) {
            if (!$this->isProductAssignedToCategory($row[self::CATEGORY_COLUMN], $row[self::SKU_COLUMN])) {
                $nonAssignedSkus[$idx] = $row;
            }
        }

        return $nonAssignedSkus;
    }

    private function isProductAssignedToCategory($categoryId, string $skuToValidate): bool
    {
        return in_array($skuToValidate, $this->getCategorySkus($categoryId), true);
    }

    private function getCategorySkus($categoryId): array
    {
        return $this->categoryProductsSkuList->getCategoryProductSkus($categoryId);
    }
}
