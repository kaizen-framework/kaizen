<?php

declare(strict_types=1);

namespace App\Components\Config\Schema\Node\Builder;

use App\Components\Config\Schema\ConfigSchemaBuilder;
use App\Components\Config\Schema\Node\ObjectVariableNode;
use App\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ObjectVariableNodeBuilder
{
    private ?ConfigPrototypeInterface $prototype = null;

    public function __construct(
        private readonly string $key,
        private readonly ConfigSchemaBuilder $parent
    ) {}

    public function withPrototype(ConfigPrototypeInterface $prototype): self
    {
        $this->prototype = $prototype;

        return $this;
    }

    public function buildNode(): ConfigSchemaBuilder
    {
        $node = new ObjectVariableNode($this->key, $this->prototype);

        $this->parent->add($node);

        return $this->parent;
    }
}