<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model;

use Limitless\SortCategoryProducts\Model\ValidationStep\Validation\ValidateBatchData;
use Limitless\SortCategoryProducts\Model\ValidationStep\BatchImportFileParser;
use Limitless\SortCategoryProducts\Test\FileFixtureTrait;

class ValidationStepTest extends \PHPUnit_Framework_TestCase
{
    use FileFixtureTrait;
    
    private function createStepWithFile(string $fileName, array $errors = []): ValidationStep
    {
        return new ValidationStep($fileName, $this->createStubValidator($errors), $this->createStubParser());
    }
    
    private function createStepWithData(array $importData): ValidationStep
    {
        return new ValidationStep(__FILE__, $this->createStubValidator(), $this->createStubParser($importData));
    }
    
    private function createStubValidator(array $errors = []): ValidateBatchData
    {
        /** @var ValidateBatchData $validator */
        $validator = new class($errors) extends ValidateBatchData
        {
            private $errors;

            public function __construct(array $errors)
            {
                $this->errors = $errors;
            }

            public function getValidationErrors(array $origBatchData): array
            {
                return $this->errors;
            }
        };
        return $validator;
    }

    private function createStubParser(array $data = []): BatchImportFileParser
    {
        /** @var BatchImportFileParser $parser */
        $parser = new class($data) extends BatchImportFileParser
        {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function parseString(string $content): array
            {
                return $this->data;
            }

            public function parseFile(string $fileName): array
            {
                return $this->data;
            }
        };
        return $parser;
    }
    
    public function testThrowsExceptionIfFileDoesNotExist()
    {
        $expectedMessage = 'Category products sort order batch import file "foo" not found.';
        $this->setExpectedException(\RuntimeException::class, $expectedMessage);
        $this->createStepWithFile('foo');
    }

    public function testThrowsExceptionIfFileIsNotReadable()
    {
        $fileName = $this->makeTempFile();
        
        $expectedMessage = sprintf('Category products sort order batch import file "%s" is not readable.', $fileName);
        $this->setExpectedException(\RuntimeException::class, $expectedMessage);

        chmod($fileName, 0000);
        $this->createStepWithFile($fileName);
    }

    public function testThrowsExceptionIfFileIsNotAFile()
    {
        $fileName = $this->makeTempFile();
        unlink($fileName);
        mkdir($fileName);
        
        $expectedMessage = sprintf('Category products sort order batch import file "%s" is not a file.', $fileName);
        $this->setExpectedException(\RuntimeException::class, $expectedMessage);

        $this->createStepWithFile($fileName);
    }

    public function testReturnsBatchImportData()
    {
        $importData = ['foo', 'bar', 'baz'];
        $step = $this->createStepWithData($importData);
        $this->assertSame($importData, $step->getImportData());
    }

    public function testReturnsTheValidationErrors()
    {
        $errors = [['Error Message 1'], ['Error Message 2']];
        $step = $this->createStepWithFile(__FILE__, $errors);

        $this->assertSame($errors, $step->getErrorMessages());
    }
}
