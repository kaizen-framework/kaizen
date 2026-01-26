<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

/**
 * @extends Node<string>
 */
class StringNode extends Node
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

        if (!is_string($value)){
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                sprintf('Expected a string, but got %s.', get_debug_type($value))
            );
        }

        return $validationResult;
    }
}
