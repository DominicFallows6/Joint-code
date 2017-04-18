<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Test;

trait FileFixtureTrait
{
    /**
     * @var string[]
     */
    private $tempFiles = [];

    private function makeTempFile(string $content = ''): string
    {
        $fileName = tempnam(sys_get_temp_dir(), 'test-validation-step');
        file_put_contents($fileName, $content);
        $this->tempFiles[] = $fileName;

        return $fileName;
    }

    private function removeFile(string $fileName)
    {
        if (file_exists($fileName)) {
            chmod($fileName, 0700);
            $rm = is_dir($fileName) ? 'rmdir' : 'unlink';
            $rm($fileName);
        }
    }

    /**
     * @after
     */
    public function cleanUpTempFiles()
    {
        array_map([$this, 'removeFile'], $this->tempFiles);
    }
}
