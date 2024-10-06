<?php

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;

class StringPrototype implements ConfigPrototypeInterface
{
    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_string($item)) {
                throw new InvalidNodeTypeException('Invalid prototype the array must contains only strings.');
            }
        }
    }
}