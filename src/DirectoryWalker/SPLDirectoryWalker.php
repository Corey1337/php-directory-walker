<?php

declare(strict_types=1);

namespace App\DirectoryWalker;

use App\DirectoryWalker\Exceptions\DirectoryWalkerDirectoryNotFoundException;

final class SPLDirectoryWalker implements DirectoryWalkerInterface
{
    /**
     * @return \Generator<string> Absolute paths to files
     *
     * @throws DirectoryWalkerDirectoryNotFoundException
     */
    public function findTargetFiles(string $absoluteStartDirectoryPath, string $targetFilename): iterable
    {
        if (!is_dir($absoluteStartDirectoryPath)) {
            throw new DirectoryWalkerDirectoryNotFoundException("Failed to resolve path to directory: {$absoluteStartDirectoryPath}");
        }

        $directoryIterator = new \RecursiveDirectoryIterator($absoluteStartDirectoryPath, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        foreach ($iterator as $file) {
            if ($file->getFilename() === $targetFilename) {
                yield $file->getPathname();
            }
        }
    }
}
