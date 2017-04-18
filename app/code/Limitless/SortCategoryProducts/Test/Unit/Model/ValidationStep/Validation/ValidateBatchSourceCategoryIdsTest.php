<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchSourceCategoryIdsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CategoryIdList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCategoryIdList;

    private function createValidator(): ValidateBatchSourceCategoryIds
    {
        return new ValidateBatchSourceCategoryIds($this->stubCategoryIdList);
    }

    protected function setUp()
    {
        $this->stubCategoryIdList = $this->getMockBuilder(CategoryIdList::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testImplementsValidatesBatchSourceValidatorInterface()
    {
        $this->assertInstanceOf(ValidateBatchSourceInterface::class, $this->createValidator());
    }

    public function testReturnsNoErrorsForEmptyCategoryIdList()
    {
        $this->assertSame([], $this->createValidator()->getErrors([], []));
    }

    public function testReturnsErrorsForListOfNonExistingCategoryIds()
    {
        $expected = [
            0 => ['The category ID %1 does not exist.', 3],
            1 => ['The category ID %1 does not exist.', 2],
            2 => ['The category ID %1 does not exist.', 5],
        ];
        $batchSource = [
            [3, 'foo', 10],
            [2, 'bar', 20],
            [5, 'baz', 30]
        ];
        $this->assertSame($expected, $this->createValidator()->getErrors($batchSource, $batchSource));
    }

    public function testReturnsErrorsOnlyForNonExistingCategoryIds()
    {
        $batchSource = [
            [3, 'foo', 10],
            [2, 'bar', 20],
            [5, 'baz', 30]
        ];
        $this->stubCategoryIdList->method('getAllCategoryIds')->willReturn(['1', '2', '3', '4']);
        $expected = [2 => ['The category ID %1 does not exist.', 5]];
        $this->assertSame($expected, $this->createValidator()->getErrors($batchSource, $batchSource));
    }
}
