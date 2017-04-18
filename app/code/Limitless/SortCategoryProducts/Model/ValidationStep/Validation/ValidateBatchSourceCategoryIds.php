<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceCategoryIds implements ValidateBatchSourceInterface
{
    /**
     * @var CategoryIdList
     */
    private $categoryIdList;

    public function __construct(CategoryIdList $categoryIdList)
    {
        $this->categoryIdList = $categoryIdList;
    }

    public function getErrors(array $origBatchSource, array $validBatchSource): array
    {
        $errors = $this->getInvalidCategoryIds(array_column($origBatchSource, self::CATEGORY_COLUMN));

        return array_map(function ($invalidCategoryId): array {
            return ['The category ID %1 does not exist.', $invalidCategoryId];
        }, $errors);
    }

    private function getInvalidCategoryIds(array $categoryIdsToValidate): array
    {
        $existingCategoryIds = (array) $this->categoryIdList->getAllCategoryIds();

        return $this->collectInvalidCatgoryIds($existingCategoryIds, $categoryIdsToValidate);
    }

    private function collectInvalidCatgoryIds(array $existingCategoryIds, array $categoryIdsToValidate)
    {
        $invalidCategoryIds = [];
        $categoryIdMap = array_flip($existingCategoryIds);
        foreach ($categoryIdsToValidate as $idx => $categoryId) {
            if (!array_key_exists($categoryId, $categoryIdMap)) {
                $invalidCategoryIds[$idx] = $categoryId;
            }
        }

        return $invalidCategoryIds;
    }
}
