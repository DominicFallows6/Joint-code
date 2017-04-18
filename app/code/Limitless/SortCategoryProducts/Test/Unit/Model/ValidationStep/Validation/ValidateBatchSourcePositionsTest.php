<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourcePositionsTest extends \PHPUnit_Framework_TestCase
{
    public function testImplementsValidateBatchSourceInterface()
    {
        $this->assertInstanceOf(ValidateBatchSourceInterface::class, new ValidateBatchSourcePositions());
    }
    
    public function testReturnsNoErrorsForEmptyArray()
    {
        $this->assertSame([], (new ValidateBatchSourcePositions())->getErrors([], []));
    }

    public function testReturnsErrorsForNonIntegerPositions()
    {
        $batchSource = [
            [1, 'abc', 'foo'],
            [1, 'def', 'bar'],
        ];
        
        $expected = [
            0 => ['The position "%1" has to be an integer.', 'foo'],
            1 => ['The position "%1" has to be an integer.', 'bar'],
        ];
        
        $this->assertSame($expected, (new ValidateBatchSourcePositions())->getErrors($batchSource, $batchSource));
    }

    public function testReturnsNoErrorsForIntegerPositions()
    {
        $batchSource = [
            [1, 'abc', '1'],
            [1, 'def', 2],
        ];
        
        $this->assertSame([], (new ValidateBatchSourcePositions())->getErrors($batchSource, $batchSource));
    }

    public function testReturnsOnlyRecordsWithInvalidPositions()
    {
        $batchSource = [
            [1, 'abc', '1'],
            [1, 'def', 2],
            [2, 'edg', 'foo'],
            [2, 'hij', 5],
        ];
        
        $expected = [2 => ['The position "%1" has to be an integer.', 'foo']];
        
        $this->assertSame($expected, (new ValidateBatchSourcePositions())->getErrors($batchSource, $batchSource));
    }
}
