<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

class FloatPrototype extends AbstractPrototype
{
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        foreach ($values as $key => $value) {
            if (!is_float($value)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    'Expected an array of float.'
                );
            }
        }

        return $validationResult;
    }
}