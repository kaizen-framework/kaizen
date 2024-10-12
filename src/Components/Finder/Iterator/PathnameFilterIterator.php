<?php

declare(strict_types=1);

namespace Kaizen\Components\Finder\Iterator;

use Kaizen\Components\Finder\Utils\SplFileInfo;

/**
 * @template-extends FilterIterator<string, SplFileInfo>
 */
class PathnameFilterIterator extends FilterIterator
{
    #[\Override]
    public function accept(): bool
    {
        $filename = $this->currentValue()->getRelativePath();

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $filename = str_replace('\\', '/', $filename);
        }

        return $this->isAccepted($filename);
    }
}
