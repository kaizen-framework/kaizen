<?php

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;

class ScalarPrototype implements ConfigPrototypeInterface
{
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_scalar($item)) {
                throw new InvalidNodeTypeException('Invalid value the array must contains only scalar values.');
            }
        }
    }
}