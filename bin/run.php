<?php

declare(strict_types=1);

use App\AppRunner;
use App\DirectoryWalker\SPLDirectoryWalker;
use App\FileContentReader\FileContentReader;

require __DIR__.'/../vendor/autoload.php';

try {
    AppRunner::run(
        $argv,
        new SPLDirectoryWalker(),
        new FileContentReader(),
    );
} catch (Throwable $exception) {
    fputs(STDERR, $exception->getMessage().PHP_EOL);
}
