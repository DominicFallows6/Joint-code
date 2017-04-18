<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model;

use Limitless\SortCategoryProducts\Model\ProcessStep\ApplyBatchSorting;

class ProcessStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ApplyBatchSorting|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockApplyBatchSorting(): ApplyBatchSorting
    {
        return $this->getMockBuilder(ApplyBatchSorting::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAppliesAllUpdatesIfNoErrors()
    {
        $batchData = [
            [10, 'foo', 42],
        ];
        $rowNumbersWithErrors = [];

        $mockApplyBatchSorting = $this->createMockApplyBatchSorting();
        $mockApplyBatchSorting->expects($this->once())->method('apply')->with($batchData);

        (new ProcessStep($mockApplyBatchSorting))->setCategoryProductPositions($batchData, $rowNumbersWithErrors);
    }

    public function testAppliesNoUpdatesIfAllRowsHaveErrors()
    {
        $batchData = [
            0 => [10, 'foo', 42],
            1 => [10, 'bar', 43],
        ];
        $rowNumbersWithErrors = [0, 1];

        $mockApplyBatchSorting = $this->createMockApplyBatchSorting();
        $mockApplyBatchSorting->expects($this->once())->method('apply')->with([]);

        (new ProcessStep($mockApplyBatchSorting))->setCategoryProductPositions($batchData, $rowNumbersWithErrors);
    }

    public function testAppliesOnlyUpdatesWithNoErrors()
    {
        $batchData = [
            0 => [10, 'foo', 42],
            1 => [10, 'bar', 43],
        ];
        $rowNumbersWithErrors = [1];

        $mockApplyBatchSorting = $this->createMockApplyBatchSorting();
        $mockApplyBatchSorting->expects($this->once())->method('apply')->with([0 => [10, 'foo', 42]]);

        (new ProcessStep($mockApplyBatchSorting))->setCategoryProductPositions($batchData, $rowNumbersWithErrors);
    }
}
