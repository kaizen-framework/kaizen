<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

class NumericPrototype extends AbstractPrototype
{
    #[\Override]
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        foreach ($values as $key => $value) {
            if (!is_int($value) && !is_float($value)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    'Expected an array of numeric values.'
                );
            }
        }

        return $validationResult;
    }
}
