<?php

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\NodeInterface;

class TuplePrototype implements ConfigPrototypeInterface
{
    private array $types;

    public function __construct(
        TupleTypesEnum ...$types
    ) {
        $this->types = $types;
    }

    public function validatePrototype(array $array): void
    {
        if (count($array) !== count($this->types)) {
            throw new InvalidNodeTypeException();
        }

        foreach ($array as $key => $value) {
            $expectedType = $this->types[$key];

            if (
                (TupleTypesEnum::SCALAR === $expectedType && !is_scalar($value))
                || (TupleTypesEnum::SCALAR !== $expectedType && gettype($value) !== $expectedType->value)
            ) {
                throw new InvalidNodeTypeException();
            }
        }
    }
}