<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Validation\Numeric;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;

/**
 * @template T of int|float
 *
 * @implements NodeConstraintValidatorInterface<T>
 */
readonly class MinConstraintValidator implements NodeConstraintValidatorInterface
{
    public function __construct(
        private int|float $min
    ) {}

    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        return $value >= $this->min;
    }

    public function getMessage(): string
    {
        return sprintf('The value must be greater than or equal to %s', $this->min);
    }
}