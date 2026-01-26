<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

class ScalarPrototype extends AbstractPrototype
{
    #[\Override]
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext,
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        foreach ($values as $key => $value) {
            if (!is_scalar($value)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    'Expected an array of scalar values.'
                );
            }
        }

        return $validationResult;
    }
}
