<?php

declare(strict_types=1);

use App\DirectoryWalker\DirectoryWalker;
use App\FileContentReader\FileContentReader;

require __DIR__.'/../vendor/autoload.php';

const DEFAULT_TARGET_NAME = 'count';

$path = $argv[1] ?? '';

try {
    $absolutePath = realpath($path);
    if (false === $absolutePath) {
        throw new RuntimeException('Unable to locate absolute path');
    }

    $walker = new DirectoryWalker($absolutePath, DEFAULT_TARGET_NAME);
    $fileReader = new FileContentReader();

    $num = 0;
    foreach ($walker->findTargetFiles() as $file) {
        $num += $fileReader->readNumbersSumFromFile($file);
    }

    fputs(STDOUT, 'The sum of numbers found: '.$num.PHP_EOL);
} catch (Throwable $exception) {
    fputs(STDERR, $exception->getMessage().PHP_EOL);
}
