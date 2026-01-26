<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;

class TuplePrototype extends AbstractPrototype
{
    /** @var TupleTypesEnum[] */
    private array $types;

    public function __construct(
        TupleTypesEnum ...$tupleTypesEnum
    ) {
        $this->types = $tupleTypesEnum;
    }

    #[\Override]
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext,
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        $expectedTypes = array_map(static fn (TupleTypesEnum $tupleType) => $tupleType->value, $this->types);

        $lastProcessedKey = 0;

        foreach ($values as $key => $value) {
            $expectedType = $this->types[$key];

            if (
                (TupleTypesEnum::SCALAR === $expectedType && !is_scalar($value))
                || (TupleTypesEnum::SCALAR !== $expectedType && get_debug_type($value) !== $expectedType->value)
            ) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    sprintf('Expected a tuple array of [%s].', implode(', ', $expectedTypes))
                );
            }

            $lastProcessedKey = $key;
        }

        if ($lastProcessedKey !== count($this->types) - 1) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($lastProcessedKey),
                sprintf('Expected a tuple array of [%s].', implode(', ', $expectedTypes))
            );
        }

        return $validationResult;
    }
}
