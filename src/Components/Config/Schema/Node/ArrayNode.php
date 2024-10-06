<?php

namespace App\Components\Config\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ArrayNode extends Node
{
    public function __construct(
        private readonly string $key,
        private readonly ?ConfigPrototypeInterface $arrayPrototype = null,
    ) {}

    public function validateType(mixed $value): void
    {
        if(!is_array($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "array", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }

        $this->arrayPrototype?->validatePrototype($value);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}