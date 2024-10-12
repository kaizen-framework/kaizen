<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;

class NumericNode extends Node
{
    private int|float $min;
    private int|float $max;

    public function __construct(
        public readonly string $key,
    ) {}

    public function validateType(mixed $value): void
    {
        if (!is_float($value) && !is_int($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "float" or "integer", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function min(int|float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMin(): int|float
    {
        return $this->min;
    }

    public function max(int|float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function getMax(): int|float
    {
        return $this->max;
    }

    /**
     * @throws ConfigProcessingException
     */
    public function processValue(mixed $value): mixed
    {
        $value = parent::processValue($value);

        if ($this->min > $value) {
            throw new ConfigProcessingException(sprintf(
                'The node "%s" value must be at least "%s", "%s" given',
                $this->getKey(),
                $this->min,
                $value
            ));
        }

        if ($this->max < $value) {
            throw new ConfigProcessingException(sprintf(
                'The node "%s" value must not exceed "%s", "%s" given',
                $this->getKey(),
                $this->max,
                $value
            ));
        }

        return $value;
    }
}