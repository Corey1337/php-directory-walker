<?php

declare(strict_types=1);

namespace App\DirectoryWalker;

use App\DirectoryWalker\Exceptions\DirectoryWalkerDirectoryNotFoundException;

final class StackDirectoryWalker implements DirectoryWalkerInterface
{
    /**
     * Scan directory and all subdirectories to find the all target files.
     *
     * @return \Generator<string> Absolute paths to files
     *
     * @throws DirectoryWalkerDirectoryNotFoundException
     */
    public function findTargetFiles(string $absoluteStartDirectoryPath, string $targetFilename): iterable
    {
        if (!is_dir($absoluteStartDirectoryPath)) {
            throw new DirectoryWalkerDirectoryNotFoundException("Failed to resolve path to directory: {$absoluteStartDirectoryPath}");
        }

        $directoriesStack = [$absoluteStartDirectoryPath];

        while (!empty($directoriesStack)) {
            // Get directory from the stack
            $absoluteDirectoryPath = array_pop($directoriesStack);
            $directoryHandle = opendir($absoluteDirectoryPath);

            // Skip directory if cant open
            if (false === $directoryHandle) {
                continue;
            }

            // Read the entire contents of the directory
            while (($directoryElement = readdir($directoryHandle)) !== false) {
                // Skip special symbols
                if ('.' === $directoryElement || '..' === $directoryElement) {
                    continue;
                }

                // Make absolute path to element
                $absoluteDirectoryElementPath = $absoluteDirectoryPath.DIRECTORY_SEPARATOR.$directoryElement;

                // Skip if element is a link
                if (is_link($absoluteDirectoryElementPath)) {
                    continue;
                }

                // Add to stack if element is a directory
                if (is_dir($absoluteDirectoryElementPath)) {
                    $directoriesStack[] = $absoluteDirectoryElementPath;

                    continue;
                }

                // Return path to target element
                if (
                    is_file($absoluteDirectoryElementPath)
                    && $directoryElement === $targetFilename
                ) {
                    yield $absoluteDirectoryElementPath;
                }
            }

            // Close directory handle after processing all elements
            closedir($directoryHandle);
        }
    }
}
