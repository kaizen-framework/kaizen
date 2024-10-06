<?php

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;

interface ConfigPrototypeInterface
{
    /**
     * @throws InvalidNodeTypeException
     */
    public function validatePrototype(array $array): void;
}