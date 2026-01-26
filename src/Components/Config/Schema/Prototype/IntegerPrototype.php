<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

class IntegerPrototype extends AbstractPrototype
{
    #[\Override]
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        foreach ($values as $key => $value) {
            if (!is_int($value)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    'Expected an array of integers.'
                );
            }
        }

        return $validationResult;
    }
}
