<?php

declare(strict_types=1);

namespace Kaizen\Components\Finder\Iterator;

use Kaizen\Components\Finder\Utils\SplFileInfo;

class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    #[\Override]
    public function current(): SplFileInfo
    {
        return new SplFileInfo($this->getPathname(), $this->getSubPath());
    }
}
