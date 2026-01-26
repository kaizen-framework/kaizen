<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;

/**
 * @extends Node<array<string, mixed>>
 */
class ObjectVariableNode extends Node
{
    public function __construct(
        string $key,
        private readonly ?AbstractPrototype $prototype = null,
    ) {
        parent::__construct($key);
    }

    #[\Override]
    public function isValueSatisfiesNodeSpecification(
        mixed $value,
        ValidationContext $validationContext,
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesNodeSpecification($value, $validationContext);

        if (!$validationResult->isValid() || null === $value) {
            return $validationResult;
        }

        if (!is_array($value)) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                sprintf(
                    'Expected a list of values, but got %s.',
                    get_debug_type($value)
                )
            );

            return $validationResult;
        }

        $intKey = array_find(array_keys($value), static fn(mixed $key) => !is_string($key));

        if (null !== $intKey) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                'All children should have a key.',
            );

            return $validationResult;
        }

        if (!$this->prototype) {
            return $validationResult;
        }

        return $validationResult->merge($this->prototype->isValueSatisfiesPrototypeSpecification(
            $value,
            $validationContext->child($this->key)
        ));
    }

    public function normalize(mixed $value): ?array
    {
        $value = parent::normalize($value);

        if (!$this->prototype || null === $value) {
            return $value;
        }

        return $this->prototype->normalize($value);
    }
}
