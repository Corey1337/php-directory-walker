<?php

declare(strict_types=1);

namespace App\DirectoryWalker;

interface DirectoryWalkerInterface
{
    public function findTargetFiles(string $absoluteStartDirectoryPath, string $targetFilename): iterable;
}
