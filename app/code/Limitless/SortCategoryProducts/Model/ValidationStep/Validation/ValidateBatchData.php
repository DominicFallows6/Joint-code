<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchData
{
    /**
     * @var ValidateBatchSourceInterface[]
     */
    private $validators;

    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    public function getValidationErrors(array $origBatchData): array
    {
        $validBatchData = $origBatchData;
        $mergedErrors = [];
        foreach ($this->validators as $validator) {
            $errors = $validator->getErrors($origBatchData, $validBatchData);
            $mergedErrors = $this->mergeErrors($mergedErrors, $errors);
            $validBatchData = $this->removeRowsWithErrors($validBatchData, $errors);
        }
        return $mergedErrors;
    }

    private function mergeErrors(array $errors, array $validatorErrors): array 
    {
        foreach ($validatorErrors as $idx => $error) {
            $errors[$idx][] = $error;
        }
        return $errors;
    }

    private function removeRowsWithErrors(array $validBatchData, array $errors)
    {
        foreach (array_keys($errors) as $idxToRemove) {
            unset($validBatchData[$idxToRemove]);
        }
        return $validBatchData;
    }
}
