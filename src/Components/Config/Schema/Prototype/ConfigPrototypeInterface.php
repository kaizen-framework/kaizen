<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\ConfigSchema;

interface ConfigPrototypeInterface
{
    /**
     * @param array<int, mixed> $array
     *
     * @throws InvalidNodeTypeException
     */
    public function validatePrototype(array $array): void;

    /**
     * @param array<int, mixed> $array
     *
     * @return array<int, mixed>
     */
    public function processPrototype(array $array): array;
}