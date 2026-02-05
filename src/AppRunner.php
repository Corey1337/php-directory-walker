<?php

namespace App;

use App\DirectoryWalker\DirectoryWalkerInterface;
use App\FileContentReader\FileContentReaderInterface;

final class AppRunner
{
    private const string TARGET_FILE_NAME = 'count';

    /** @param iterable<string> $commandArguments */
    public static function run(
        array $commandArguments,
        DirectoryWalkerInterface $walker,
        FileContentReaderInterface $fileReader,
    ): void {
        $path = $commandArguments[1] ?? '';
        $absolutePath = realpath($path);
        if (false === $absolutePath) {
            throw new \RuntimeException('Unable to locate absolute path');
        }

        $num = 0;
        foreach ($walker->findTargetFiles($absolutePath, self::TARGET_FILE_NAME) as $file) {
            $num += $fileReader->readNumbersSumFromFile($file);
        }

        fputs(STDOUT, 'The sum of numbers found: '.$num.PHP_EOL);
    }
}
