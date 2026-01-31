<?php

declare(strict_types=1);

namespace App\FileContentReader;

interface FileContentReaderInterface
{
    public function readNumbersSumFromFile(string $absoluteFilePath);
}
