<?php

namespace Kaizen\Components\Config\Schema\Validation;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;

/**
 * @template T
 */
interface NodeConstraintValidatorInterface
{
    /**
     * @param T $value
     * @param Node<T> $node
     */
    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool;

    public function getMessage(): string;
}
