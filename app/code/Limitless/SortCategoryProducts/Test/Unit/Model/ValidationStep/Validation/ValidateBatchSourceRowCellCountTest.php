<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceRowCellCountTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsValidateBatchSourceInterface()
    {
        $this->assertInstanceOf(ValidateBatchSourceInterface::class, new ValidateBatchSourceRowCellCount());
    }

    public function testReturnsNoErrorsForEmptyArray()
    {
        $this->assertSame([], (new ValidateBatchSourceRowCellCount())->getErrors([], []));
    }
    
    public function testReturnsNoErrorIfCellCountIsExpectedValue()
    {
        $this->assertSame([], (new ValidateBatchSourceRowCellCount())->getErrors([[123, 'foo', 42]], []));
    }

    public function testReturnsErrorForRowsWithToFewCells()
    {
        $expectedColumnCount = ValidateBatchSourceRowCellCount::EXPECTED_COLUMN_COUNT;
        
        $invalidBatchData = [
            0 => [100, 'foo'],
            1 => ['bar']
        ];
        
        $expected = [
            0 => ['Column count to litte, expected %1, got %2', $expectedColumnCount, 2],
            1 => ['Column count to litte, expected %1, got %2', $expectedColumnCount, 1],
        ];
        
        $this->assertSame($expected, (new ValidateBatchSourceRowCellCount())->getErrors($invalidBatchData, []));
    }

    public function testReturnsErrorForRowsWithToMany()
    {
        $expectedColumnCount = ValidateBatchSourceRowCellCount::EXPECTED_COLUMN_COUNT;
        
        $invalidBatchData = [
            0 => [100, 'foo', 'bar', 5],
        ];
        
        $expected = [
            0 => ['Column count to large, expected %1, got %2', $expectedColumnCount, 4],
        ];
        
        $this->assertSame($expected, (new ValidateBatchSourceRowCellCount())->getErrors($invalidBatchData, []));
    }
}
