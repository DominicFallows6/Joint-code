<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourcePositions implements ValidateBatchSourceInterface
{
    public function getErrors(array $origBatchSource, array $validBatchSource): array
    {
        $errors = $this->getInvalidPositions(array_column($origBatchSource, self::POSITION_COLUMN));
        return array_map(function ($invalidPosition): array {
            return ['The position "%1" has to be an integer.', $invalidPosition];
        }, $errors);
    }

    private function getInvalidPositions(array $positionsToValidate): array
    {
        $invalidPositions = [];
        foreach ($positionsToValidate as $idx => $position) {
            if (!is_int($position) && !preg_match('#^\d+$#', $position)) {
                $invalidPositions[$idx] = $position;
            }
        }

        return $invalidPositions;
    }
}
