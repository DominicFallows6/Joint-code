<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceRowCellCount implements ValidateBatchSourceInterface
{
    const EXPECTED_COLUMN_COUNT = 3;
    
    public function getErrors(array $origBatchSource, array $validBatchSource): array
    {
        $errors = [];
        foreach ($origBatchSource as $idx => $record) {
            if (count($record) < self::EXPECTED_COLUMN_COUNT) {
                $errors[$idx] = $this->buildCellCountTooSmallError($record);
            } elseif (count($record) > self::EXPECTED_COLUMN_COUNT) {
                $errors[$idx] = $this->buildCellCountTooLargeError($record);
            }
        }

        return $errors;
    }

    private function buildCellCountTooSmallError(array $record): array
    {
        return ['Column count to litte, expected %1, got %2', self::EXPECTED_COLUMN_COUNT, count($record)];
    }

    private function buildCellCountTooLargeError(array $record): array
    {
        return ['Column count to large, expected %1, got %2', self::EXPECTED_COLUMN_COUNT, count($record)];
    }
}
