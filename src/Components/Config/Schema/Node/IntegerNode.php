<?php

namespace App\Components\Config\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;

class IntegerNode extends NumericNode
{
    #[\Override]
    public function validateType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "integer", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }
    }
}