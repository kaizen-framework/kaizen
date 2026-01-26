<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

/**
 * A prototype is a model that nodes with children (such as array or object nodes) should follow.
 */
abstract class AbstractPrototype
{
    /**
     * Apply transformations on a value based on the prototype specification, then return the value.
     *
     * @template T of array<string, mixed>|array<int, mixed>
     *
     * @param T $array
     *
     * @return T
     */
    public function normalize(array $array): array
    {
        return $array;
    }

    /**
     * Validate that the value provided satisfies the prototype type specification.
     *
     * @param array<int|string, mixed> $values
     */
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        if (null === $validationContext->path) {
            throw new \RuntimeException(
                'An error occurred while attempting to process the prototype validation. The validation context must comes from the parent node'
            );
        }

        return new ValidationResult();
    }
}
