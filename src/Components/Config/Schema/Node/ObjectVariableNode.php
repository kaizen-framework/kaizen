<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ObjectVariableNode extends Node
{
    public function __construct(
        private readonly string $key,
        private readonly ?ConfigPrototypeInterface $prototype = null,
    ) {}

    public function validateType(mixed $value): void
    {
        if (!is_array($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "object", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }

        foreach ($value as $key => $currentValue) {
            if (!is_string($key)) {
                throw new InvalidNodeTypeException(sprintf(
                    'Invalid entry "%s" for the node "%s", all the entries should have a key',
                    $key,
                    $this->getKey()
                ));
            }
        }

        if (!$this->prototype) {
            return;
        }

        $this->prototype->validatePrototype($value);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}