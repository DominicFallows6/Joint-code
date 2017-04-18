<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

use Limitless\SortCategoryProducts\Model\BatchDataFormatInterface;

interface ValidateBatchSourceInterface
{
    const CATEGORY_COLUMN = BatchDataFormatInterface::CATEGORY_COLUMN;
    const SKU_COLUMN = BatchDataFormatInterface::SKU_COLUMN;
    const POSITION_COLUMN = BatchDataFormatInterface::POSITION_COLUMN;
    
    /**
     * Input array structure (both $origBatchSource and $validBatchSource):
     * [
     *     int row => [int category id, string sku, int position],
     *     int row => [int category id, string sku, int position],
     *     ...
     * ]
     * 
     * Output array structure:
     * [
     *     [int rowWithError => string errorMessage],
     *     [int rowWithError => string errorMessage],
     *     ...
     * ]
     * 
     * The input $validBatchSource contains only records from $origBatchSource that contained
     * no errors according to earlier validators. If no errors where detected so far or for
     * the first validator, $origBatchSource and $validBatchSource are identical.
     *
     * @param array[] $origBatchSource
     * @param array[] $validBatchSource
     * @return string[]
     */
    public function getErrors(array $origBatchSource, array $validBatchSource): array;
}
