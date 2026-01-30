<?php

declare(strict_types=1);

namespace App\DirectoryWalker;

use App\DirectoryWalker\Exceptions\DirectoryWalkerDirectoryNotFoundException;

final class DirectoryWalker implements IDirectoryWalker
{
    private string $startPath;
    private string $targetFilename;

    /** @throws DirectoryWalkerDirectoryNotFoundException */
    public function __construct(string $absoluteStartDirectoryPath, string $targetFilename)
    {
        if (!is_dir($absoluteStartDirectoryPath)) {
            throw new DirectoryWalkerDirectoryNotFoundException("Failed to resolve path to directory: {$absoluteStartDirectoryPath}");
        }

        $this->startPath = $absoluteStartDirectoryPath;

        $this->targetFilename = $targetFilename;
    }

    /** @return \Generator<string> */
    public function findTargetFiles(): iterable
    {
        $directoriesStack = [$this->startPath];

        while (!empty($directoriesStack)) {
            $absoluteDirectoryPath = array_pop($directoriesStack);
            $directoryHandle = opendir($absoluteDirectoryPath);

            if (false === $directoryHandle) {
                continue;
            }

            while (($directoryElement = readdir($directoryHandle)) !== false) {
                if ('.' === $directoryElement || '..' === $directoryElement) {
                    continue;
                }

                $absoluteDirectoryElementPath = $absoluteDirectoryPath.DIRECTORY_SEPARATOR.$directoryElement;

                if (is_link($absoluteDirectoryElementPath)) {
                    continue;
                }

                if (is_dir($absoluteDirectoryElementPath)) {
                    $directoriesStack[] = $absoluteDirectoryElementPath;

                    continue;
                }

                if (
                    is_file($absoluteDirectoryElementPath)
                    && $directoryElement === $this->targetFilename
                ) {
                    yield $absoluteDirectoryElementPath;
                }
            }

            closedir($directoryHandle);
        }
    }
}
