<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\StringNode;

class StringNodeBuilder
{
    private bool $isRequired = false;
    private ?string $defaultValue = null;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $parent
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $node = new StringNode($this->key);

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