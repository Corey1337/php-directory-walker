<?php

declare(strict_types=1);

namespace App\Tests;

use App\DirectoryWalker\DirectoryWalkerInterface;
use App\DirectoryWalker\SPLDirectoryWalker;
use App\DirectoryWalker\StackDirectoryWalker;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClassesThatImplementInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClassesThatImplementInterface(DirectoryWalkerInterface::class)]
class DirectoryWalkerTest extends TestCase
{
    public const string TEST_TARGET_FILE_NAME = 'test_count';

    #[DataProvider('provideCombinedTestCases')]
    public function testFindTargetFiles(DirectoryWalkerInterface $directoryWalker, array $structure, int $exceptedFilesCount)
    {
        $root = vfsStream::setup('test_root', null, $structure);
        $this->assertCount($exceptedFilesCount, iterator_to_array($directoryWalker->findTargetFiles($root->url(), self::TEST_TARGET_FILE_NAME)));
    }

    public static function provideCombinedTestCases(): \Generator
    {
        $implementations = self::provideDirectoryWalkerImplementation();

        foreach ($implementations as $implementationCaseName => $implementation) {
            $testCases = self::provideFileSystemStructureCases();
            foreach ($testCases as $testCaseName => $testCase) {
                yield "{$implementationCaseName} | {$testCaseName}" => array_merge($implementation, $testCase);
            }
        }
    }

    public static function provideDirectoryWalkerImplementation(): \Generator
    {
        yield 'directory walker based on stack' => [new StackDirectoryWalker()];

        yield 'directory walker based on SPL RecursiveIterator' => [new SPLDirectoryWalker()];
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
