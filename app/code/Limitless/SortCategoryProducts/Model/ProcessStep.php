<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model;

use Limitless\SortCategoryProducts\Model\ProcessStep\ApplyBatchSorting;

class ProcessStep
{
    /**
     * @var ApplyBatchSorting
     */
    private $applyBatchSorting;

    public function __construct(ApplyBatchSorting $applyBatchSorting)
    {
        $this->applyBatchSorting = $applyBatchSorting;
    }

    public function setCategoryProductPositions(array $batchData, array $rowNumbersWithErrors)
    {
        foreach ($rowNumbersWithErrors as $rowNumberWithErrors) {
            unset($batchData[$rowNumberWithErrors]);
        }
        $this->applyBatchSorting->apply($batchData);
    }
}
