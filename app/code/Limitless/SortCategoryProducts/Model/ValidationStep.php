<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model;

use Limitless\SortCategoryProducts\Model\ValidationStep\Validation\ValidateBatchData;
use Limitless\SortCategoryProducts\Model\ValidationStep\BatchImportFileParser;

class ValidationStep
{
    /**
     * @var ValidateBatchData
     */
    private $validateBatchData;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var BatchImportFileParser
     */
    private $parser;

    /**
     * @param string $fileName
     * @param ValidateBatchData $validateBatchData
     */
    public function __construct($fileName, ValidateBatchData $validateBatchData, BatchImportFileParser $parser)
    {
        $this->checkFileExists($fileName);
        $this->checkFileIsReadable($fileName);
        $this->checkFileIsFile($fileName);
        $this->fileName = $fileName;
        $this->validateBatchData = $validateBatchData;
        $this->parser = $parser;
    }

    private function checkFileExists($fileName)
    {
        if (!file_exists($fileName)) {
            $message = sprintf('Category products sort order batch import file "%s" not found.', $fileName);
            throw new \RuntimeException($message);
        }
    }

    private function checkFileIsReadable($fileName)
    {
        if (!is_readable($fileName)) {
            $message = sprintf('Category products sort order batch import file "%s" is not readable.', $fileName);
            throw new \RuntimeException($message);
        }
    }

    private function checkFileIsFile($fileName)
    {
        if (!is_file($fileName)) {
            $message = sprintf('Category products sort order batch import file "%s" is not a file.', $fileName);
            throw new \RuntimeException($message);
        }
    }
    
    public function getErrorMessages()
    {
        return $this->validateBatchData->getValidationErrors($this->getImportData());
    }

    public function getImportData(): array
    {
        return $this->parser->parseFile($this->fileName);
    }
}
