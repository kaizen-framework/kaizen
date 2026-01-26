<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

/**
 * @template T of float|int
 *
 * @extends Node<T>
 */
class NumericNode extends Node
{
    #[\Override]
    public function isValueSatisfiesNodeSpecification(
        mixed $value,
        ValidationContext $validationContext,
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesNodeSpecification($value, $validationContext);

        if (!$validationResult->isValid() || null === $value) {
            return $validationResult;
        }

        if (!is_float($value) && !is_int($value)) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                sprintf(
                    'Expected a numeric value, but got %s.',
                    get_debug_type($value)
                )
            );
        }

        return $validationResult;
    }
}
