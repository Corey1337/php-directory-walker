<?php

declare(strict_types=1);

namespace App\Tests;

use App\FileContentReader\FileContentReader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FileContentReader::class)]
class FileContentReaderTest extends TestCase
{
    public const string TEST_FILE_NAME = 'test_count';
    private vfsStreamFile $file;

    protected function setUp(): void
    {
        parent::setUp();

        $root = vfsStream::setup('test_root');
        $this->file = vfsStream::newFile(self::TEST_FILE_NAME)->at($root);
    }

    #[DataProvider('provideFileContentCases')]
    public function testReadNumbersSumFromFile(string $fileContent, int $expectedNumbersSum): void
    {
        $this->file->setContent($fileContent);

        $reader = new FileContentReader();
        $number = $reader->readNumbersSumFromFile($this->file->url());

        $this->assertEquals($expectedNumbersSum, $number);
    }

    public static function provideFileContentCases(): \Generator
    {
        yield 'simple test' => ['10', 10];

        yield 'text after numbers' => ['10abc 13 abcabc', 1013];

        yield 'numbers after text' => ['true abc10 12', 1012];

        yield 'starting from zero' => ['010', 10];

        yield 'numbers with spaces' => ['3 1 4', 314];

        yield 'simple multiline' => ['3
                 1
                 44
            5',
            53,
        ];

        yield 'multiline with text' => ['3
              true   1
               false  44
             sad  5',
            53,
        ];
    }
}
