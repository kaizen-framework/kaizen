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
readonly class CompositeValidator implements NodeConstraintValidatorInterface
{
    /**
     * @param \Closure(T): bool $when
     * @param (\Closure(T, NodeListSchema): bool)|bool $then
     */
    public function __construct(
        private \Closure $when,
        private \Closure|bool $then,
        private string $message
    ) { }

    #[\Override]
    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        if (!($this->when)($value)) {
            return true;
        }

        if (is_callable($this->then)) {
            return ($this->then)($value, $nodeListSchema);
        }

        return $this->then;
    }

    #[\Override]
    public function getMessage(): string
    {
        return $this->message;
    }
}