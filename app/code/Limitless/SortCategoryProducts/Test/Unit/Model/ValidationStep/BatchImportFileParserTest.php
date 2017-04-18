<?php

declare(strict_types=1);

namespace Limitless\SortCategoryProducts\Model\ValidationStep;

class BatchImportFileParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider batchImportFileContentProvider
     */
    public function testReturnsCsvAsArray(string $content, array $expected)
    {
        $this->assertSame($expected, (new BatchImportFileParser())->parseString($content));
    }

    public function batchImportFileContentProvider(): array
    {
        return [
            'empty' => ['', []],
            '1 row' => ['1,foo,10', [['1', 'foo', '10']]],
            '2 row' => ['1,foo,10' . "\n" . '1,bar,20', [['1', 'foo', '10'], ['1', 'bar', '20']]],
            'quoted' => ['1,"foo,bar",10', [['1', 'foo,bar', '10']]],
            'quotes in cell' => ['1,foo "" bar,10', [['1', 'foo "" bar', '10']]],
            'escaped quotes' => ['1,"foo ""bar""",10', [['1', 'foo "bar"', '10']]],
        ];
    }
}
