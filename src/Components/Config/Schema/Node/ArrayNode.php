<?php

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ArrayNode extends Node
{
    public function __construct(
        private readonly string $key,
        private readonly ?ConfigPrototypeInterface $arrayPrototype = null,
    ) {}

    public function validateType(mixed $value): void
    {
        if (!is_array($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "array", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }

        $this->arrayPrototype?->validatePrototype($value);
    }

    /**
     * @return array<int, mixed>
     *
     * @throws ConfigProcessingException
     */
    #[\Override]
    public function processValue(mixed $value): array
    {
        /** @var array<int, mixed> $value */
        $value = parent::processValue($value);

        if (!$this->arrayPrototype) {
            return $value;
        }

        return $this->arrayPrototype->processPrototype($value);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
