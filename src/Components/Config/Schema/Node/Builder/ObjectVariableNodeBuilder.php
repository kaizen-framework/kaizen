<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\ObjectVariableNode;

class ObjectVariableNodeBuilder extends CompositeNodeBuilder
{
    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $nodeListSchemaBuilder
    ) {}

    public function buildNode(): NodeListSchemaBuilder
    {
        $objectVariableNode = new ObjectVariableNode($this->key, $this->prototype);

        $this->nodeListSchemaBuilder->add($objectVariableNode);

        return $this->nodeListSchemaBuilder;
    }
}
