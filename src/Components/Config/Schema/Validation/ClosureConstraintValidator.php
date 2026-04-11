<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Validation;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;

/**
 * @template T
 *
 * @implements NodeConstraintValidatorInterface<T>
 */
readonly class ClosureConstraintValidator implements NodeConstraintValidatorInterface
{
    /**
     * @param \Closure(T, NodeListSchema): bool $closure
     */
    public function __construct(
        private \Closure $closure,
        private string $message
    ) { }

    #[\Override]
    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        return ($this->closure)($value, $nodeListSchema);
    }

    #[\Override]
    public function getMessage(): string
    {
        return $this->message;
    }
}