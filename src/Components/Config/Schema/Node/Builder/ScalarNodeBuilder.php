<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;

class ScalarNodeBuilder
{
    protected bool $isRequired = false;
    protected string|int|float|bool|null $defaultValue = null;

    public function __construct(
        protected readonly string $key,
        protected readonly ConfigSchemaBuilder $parent
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(string|int|float|bool $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $node = new ScalarNode($this->key);

        if (null !== $this->defaultValue) {
            $node->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $node->required();
        }

        $this->parent->add($node);

        return $this->parent;
    }
}