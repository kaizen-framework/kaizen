<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;

/**
 * @extends Node<array<int, mixed>>
 */
class ArrayNode extends Node
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
                sprintf('Expected an array, but got %s.', get_debug_type($value))
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

    #[\Override]
    public function normalize(mixed $value): mixed
    {
        $value = parent::normalize($value);

        if (null === $this->prototype || null === $value) {
            return $value;
        }

        return $this->prototype->normalize($value);
    }
}
