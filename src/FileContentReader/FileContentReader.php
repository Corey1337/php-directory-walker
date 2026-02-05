<?php

declare(strict_types=1);

namespace App\FileContentReader;

final class FileContentReader implements FileContentReaderInterface
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

        while (false === feof($fileHandle)) {
            $line = fgets($fileHandle);
            if (false !== $line) {
                $number += intval(preg_replace('/[^0-9]/', '', $line));
            }
        }

        fclose($fileHandle);

        return $number;
    }
}
