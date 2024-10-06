<?php

declare(strict_types=1);

namespace App\Components\Config\Schema\Node\Builder;

use App\Components\Config\Schema\ConfigSchemaBuilder;
use App\Components\Config\Schema\Node\StringNode;

class StringNodeBuilder
{
    private bool $isRequired;
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