<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep;

class BatchImportFileParser
{
    public function parseString(string $content): array
    {
        return array_map('str_getcsv', array_filter(explode("\n", $content)));
    }
    
    public function parseFile(string $fileName): array
    {
        return $this->parseString(file_get_contents($fileName));
    }
}
