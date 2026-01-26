<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Validation\String;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;

/**
 * @implements NodeConstraintValidatorInterface<string>
 */
readonly class PatternConstraintValidator implements NodeConstraintValidatorInterface
{
    public function __construct(
        private string $pattern,
        private ?string $message = null,
    ) { }

    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        $patternResult = preg_match($this->pattern, $value);

        if (false === $patternResult) {
            throw new \RuntimeException(sprintf(
                'The pattern "%s" seems to be invalid.',
                $this->pattern
            ));
        }

        return 1 === $patternResult;
    }

    public function getMessage(): string
    {
        if ($this->message === null) {
            return sprintf('The value not match the designed pattern "%s"', $this->pattern);
        }

        return $this->message;
    }
}