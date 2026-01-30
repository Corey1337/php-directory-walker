<?php

declare(strict_types=1);

namespace App\DirectoryWalker;

interface IDirectoryWalker
{
    public function findTargetFiles(): iterable;
}
