<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\IntegerNode;

class IntegerNodeBuilder
{
    private ?int $min, $max, $defaultValue = null;
    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $parent
    ) {}

    public function min(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function defaultValue(int $defaultValue): self
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
        $node = new IntegerNode($this->key);

        if (isset($this->min)) {
            $node->min($this->min);
        }

        if (isset($this->max)) {
            $node->max($this->max);
        }

        if (isset($this->defaultValue)) {
            $node->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $node->required();
        }

        $this->parent->add($node);

        return $this->parent;
    }
}