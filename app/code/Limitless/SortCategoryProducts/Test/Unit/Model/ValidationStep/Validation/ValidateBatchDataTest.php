<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep\Validation;

class ValidateBatchDataTest extends \PHPUnit_Framework_TestCase
{
    private function createValidator(ValidateBatchSourceInterface ...$validators): ValidateBatchData
    {
        return new ValidateBatchData($validators);
    }
    
    private function createStubBatchDataValidator(array $errors = []): ValidateBatchSourceInterface
    {
        /** @var ValidateBatchSourceInterface $stubValidator */
        $stubValidator = new class($errors) implements ValidateBatchSourceInterface
        {
            private $errors;

            public function __construct(array $errors)
            {
                $this->errors = $errors;
            }

            public function getErrors(array $origBatchSource, array $validBatchSource): array
            {
                return $this->errors;
            }
        };

        return $stubValidator;
    }

    public function testReturnsNoErrorsForEmptyArray()
    {
        $this->assertSame([], $this->createValidator()->getValidationErrors([]));
    }

    public function testReturnsNoErrorsIfDelegateValidatorsReturnNoErrors()
    {
        $stubValidator1 = $this->createStubBatchDataValidator();
        $stubValidator2 = $this->createStubBatchDataValidator();

        $batchData = [
            [1, 'foo', 1],
            [1, 'bar', 5],
            [2, 'baz', 3],
        ];
        $this->assertSame([], $this->createValidator($stubValidator1, $stubValidator2)->getValidationErrors($batchData));
    }

    public function testReturnsTheErrorsFromABatchDataValidator()
    {
        $stubValidator = $this->createStubBatchDataValidator([0 => ['Test error message']]);

        $batchData = [
            [1, 'foo', 2],
        ];
        
        $validationErrors = $this->createValidator($stubValidator)->getValidationErrors($batchData);
        $this->assertSame([0 => [['Test error message']]], $validationErrors);
    }

    public function testMergesErrorsWithTheSameLine()
    {
        $stubValidator1 = $this->createStubBatchDataValidator([0 => ['Test error message 1']]);
        $stubValidator2 = $this->createStubBatchDataValidator([0 => ['Test error message 2']]);

        $batchData = [
            [1, 'foo', 2],
        ];

        $validationErrors = $this->createValidator($stubValidator1, $stubValidator2)->getValidationErrors($batchData);
        $this->assertSame([0 => [['Test error message 1'], ['Test error message 2']]], $validationErrors);
    }

    public function testMergesErrorsFromDifferentLines()
    {
        $stubValidator1 = $this->createStubBatchDataValidator([0 => ['Test error message 1']]);
        $stubValidator2 = $this->createStubBatchDataValidator([1 => ['Test error message 2']]);

        $batchData = [
            [1, 'foo', 20],
            [1, 'bar', 30],
        ];

        $validationErrors = $this->createValidator($stubValidator1, $stubValidator2)->getValidationErrors($batchData);
        $this->assertSame([0 => [['Test error message 1']], 1 => [['Test error message 2']]], $validationErrors);
    }

    public function testMergesErrorsFromSameAndDifferentLines()
    {
        $stubValidator1 = $this->createStubBatchDataValidator([0 => ['Message 1'], 1 => ['Message 2']]);
        $stubValidator2 = $this->createStubBatchDataValidator([0 => ['Message 3']]);
        $stubValidator3 = $this->createStubBatchDataValidator([1 => ['Message 4'], 2 => ['Message 5']]);

        $batchData = [
            [1, 'foo', 10],
            [1, 'bar', 20],
            [1, 'baz', 30],
        ];

        $validator = $this->createValidator($stubValidator1, $stubValidator2, $stubValidator3);
        $validationErrors = $validator->getValidationErrors($batchData);
        $this->assertSame([
            0 => [['Message 1'], ['Message 3']],
            1 => [['Message 2'], ['Message 4']],
            2 => [['Message 5']],
        ], $validationErrors);
    }
}
