<?php

declare(strict_types=1);

namespace App\Components\Config\Schema\Node\Builder;

use App\Components\Config\Schema\ConfigSchemaBuilder;
use App\Components\Config\Schema\Node\BooleanNode;

class BooleanNodeBuilder
{
    private ?bool $defaultValue = null;
    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $parent
    ) { }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(bool $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $node = new BooleanNode($this->key);

        if ($this->isRequired) {
            $node->required();
        }

        if (!is_null($this->defaultValue)) {
            $node->defaultValue($this->defaultValue);
        }

        $this->parent->add($node);

        return $this->parent;
    }
}