<?php

declare(strict_types=1);

namespace App\FileContentReader;

interface IFileContentReader
{
    public function readNumbersSumFromFile(string $absoluteFilePath);
}
