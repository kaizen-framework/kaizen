<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\FloatNode;

class FloatNodeBuilder
{
    private ?float $min, $max, $defaultValue = null;
    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $parent
    ) {}

    public function min(float $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function max(float $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function defaultValue(float $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function required(): self
    {
        $this->isRequired = true;
        
        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $node = new FloatNode($this->key);

        if (!is_null($this->min)) {
            $node->min($this->min);
        }

        if (!is_null($this->max)) {
            $node->max($this->max);
        }

        if (!is_null($this->defaultValue)) {
            $node->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $node->required();
        }

        $this->parent->add($node);

        return $this->parent;
    }
}