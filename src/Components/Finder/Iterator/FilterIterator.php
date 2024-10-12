<?php

declare(strict_types=1);

namespace Kaizen\Components\Finder\Iterator;

use Iterator;
use Kaizen\Components\Finder\Utils\RegexHelper;

/**
 * @template-covariant TKey
 * @template-covariant TValue
 *
 * @template-extends \FilterIterator<TKey, TValue, \Iterator<TKey, TValue>>
 */
abstract class FilterIterator extends \FilterIterator
{
    /**
     * @param \Iterator<TKey, TValue> $iterator       The iterator to filter
     * @param string[]                $matchRegexps   Array of patterns, at least one of them that should match
     * @param string[]                $noMatchRegexps Array of patterns, none of them should match
     */
    public function __construct(
        \Iterator $iterator,
        protected array $matchRegexps,
        protected array $noMatchRegexps = []
    ) {
        parent::__construct($iterator);
    }

    /**
     * @return TValue
     */
    public function currentValue()
    {
        return $this->current();
    }

    protected function isAccepted(string $name): bool
    {
        foreach ($this->noMatchRegexps as $noMatchRegexp) {
            if (!RegexHelper::isRegex($noMatchRegexp)) {
                $noMatchRegexp = RegexHelper::globToRegex($noMatchRegexp);
            }

            if (preg_match($noMatchRegexp, $name)) {
                return false;
            }
        }

        if ([] !== $this->matchRegexps) {
            foreach ($this->matchRegexps as $matchRegexp) {
                if (!RegexHelper::isRegex($matchRegexp)) {
                    $matchRegexp = RegexHelper::globToRegex($matchRegexp);
                }

                if (preg_match($matchRegexp, $name)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }
}
