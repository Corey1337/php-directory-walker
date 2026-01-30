<?php

declare(strict_types=1);

namespace App\FileContentReader;

final class FileContentReader implements IFileContentReader
{
    public function readNumbersSumFromFile(string $absoluteFilePath): int
    {
        $number = 0;

        if (!is_file($absoluteFilePath)) {
            return $number;
        }

        $fileHandle = fopen($absoluteFilePath, 'rb');
        if (false === $fileHandle) {
            return $number;
        }

        while (feof($fileHandle) === false) {
            $line = fgets($fileHandle);
            if (false !== $line) {
                $number += intval(str_replace(' ', '', $line));
            }
        }

        fclose($fileHandle);

        return $number;
    }
}
