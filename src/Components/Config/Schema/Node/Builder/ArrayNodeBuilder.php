<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Prototype\ConfigPrototypeInterface;

class ArrayNodeBuilder
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
        $node = new ArrayNode($this->key, $this->prototype);

        $this->parent->add($node);

        return $this->parent;
    }
}
