<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\ArrayNode;

class ArrayNodeBuilder extends CompositeNodeBuilder
{
    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $configSchemaBuilder
    ) {}

    public function buildNode(): NodeListSchemaBuilder
    {
        $arrayNode = new ArrayNode($this->key, $this->prototype);

        $this->configSchemaBuilder->add($arrayNode);

        return $this->configSchemaBuilder;
    }
}
