<?php

declare(strict_types=1);

namespace App\Tests;

use App\DirectoryWalker\DirectoryWalker;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DirectoryWalker::class)]
class DirectoryWalkerTest extends TestCase
{
    public const string TEST_TARGET_FILE_NAME = 'test_count';

    #[DataProvider('provideFileSystemStructureCases')]
    public function testFindTargetFiles($structure, $exceptedFilesCount)
    {
        $root = vfsStream::setup('test_root', null, $structure);

        $walker = new DirectoryWalker($root->url(), self::TEST_TARGET_FILE_NAME);

        $this->assertCount($exceptedFilesCount, iterator_to_array($walker->findTargetFiles()));
    }

    public static function provideFileSystemStructureCases(): \Generator
    {
        yield 'simple test' => [
            [self::TEST_TARGET_FILE_NAME => '123'], 1,
        ];

        yield 'test multiply files' => [
            [
                self::TEST_TARGET_FILE_NAME => '123',
                self::TEST_TARGET_FILE_NAME.'_123' => '123',
                '123_'.self::TEST_TARGET_FILE_NAME => '123',
                'non_target_file' => '312',
            ], 1,
        ];

        yield 'test nested files' => [
            [
                'empty_dir' => [],
                'dir_with_other_files' => [
                    self::TEST_TARGET_FILE_NAME.'_123' => '123',
                    '123_'.self::TEST_TARGET_FILE_NAME => '123',
                    '123_321' => '123',
                ],
                'dir_with_target_files' => [
                    self::TEST_TARGET_FILE_NAME.'_123' => '123',
                    strtoupper(self::TEST_TARGET_FILE_NAME) => '123',
                    self::TEST_TARGET_FILE_NAME => '123',
                ],
            ], 1,
        ];

        yield 'test dir with target file name' => [
            [
                self::TEST_TARGET_FILE_NAME => [],
                'dir_with_target_files' => [
                    self::TEST_TARGET_FILE_NAME => [
                        self::TEST_TARGET_FILE_NAME => '123',
                    ],
                ],
            ], 1,
        ];

        yield 'test multiply files in nested directories' => [
            [
                self::TEST_TARGET_FILE_NAME => [],
                'dir_with_target_files_1' => [
                    self::TEST_TARGET_FILE_NAME => [
                        self::TEST_TARGET_FILE_NAME => '123',
                        'some_dir_1' => [
                            self::TEST_TARGET_FILE_NAME => '123',
                        ],
                        'some_dir_2' => [
                            self::TEST_TARGET_FILE_NAME => '123',
                        ],
                    ],
                ],
                'dir_with_target_files_2' => [
                    'some_dir_1' => [
                        self::TEST_TARGET_FILE_NAME => '123',
                    ],
                    'some_dir_2' => [
                        self::TEST_TARGET_FILE_NAME => '123',
                    ],
                ],
            ], 5,
        ];
    }
}
