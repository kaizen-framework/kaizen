<?php

declare(strict_types=1);

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;

class IntegerPrototype implements ConfigPrototypeInterface
{
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_int($item)) {
                throw new InvalidNodeTypeException(sprintf(
                    'Type %s not allowed, expected : int',
                    gettype($item)
                ));
            }
        }
    }
}