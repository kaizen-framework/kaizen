<?php

declare(strict_types=1);

namespace Kaizen\Components\Finder\Iterator;

use Kaizen\Components\Finder\Utils\SplFileInfo;

/**
 * @extends FilterIterator<string, SplFileInfo>
 */
class FilenameFilterIterator extends FilterIterator
{
    #[\Override]
    public function accept(): bool
    {
        return $this->isAccepted($this->currentValue()->getFilename());
    }
}
