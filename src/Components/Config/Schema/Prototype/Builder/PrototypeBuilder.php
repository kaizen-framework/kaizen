<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Prototype\Builder;

use Kaizen\Components\Config\Schema\Node\Builder\CompositeNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Prototype\FloatPrototype;
use Kaizen\Components\Config\Schema\Prototype\IntegerPrototype;
use Kaizen\Components\Config\Schema\Prototype\NumericPrototype;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use Kaizen\Components\Config\Schema\Prototype\ScalarPrototype;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use Kaizen\Components\Config\Schema\Prototype\TuplePrototype;
use Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum;

readonly class PrototypeBuilder
{
    public function __construct(
        private CompositeNodeBuilder $nodeBuilder
    ) { }

    public function integer(): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new IntegerPrototype());

        return $this->nodeBuilder;
    }

    public function string(): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new StringPrototype());

        return $this->nodeBuilder;
    }

    public function scalar(): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new ScalarPrototype());

        return $this->nodeBuilder;
    }

    public function float(): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new FloatPrototype());

        return $this->nodeBuilder;
    }

    public function numeric(): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new NumericPrototype());

        return $this->nodeBuilder;
    }

    public function tuple(TupleTypesEnum ...$typesEnum): CompositeNodeBuilder
    {
        $this->nodeBuilder->setPrototype(new TuplePrototype(...$typesEnum));

        return $this->nodeBuilder;
    }

    /**
     * @param \Closure(NodeListSchemaBuilder): NodeListSchema $objectPrototypeBuilder
     */
    public function object(\Closure $objectPrototypeBuilder): CompositeNodeBuilder
    {
        $nodeListSchema = $objectPrototypeBuilder(new NodeListSchemaBuilder());

        $this->nodeBuilder->setPrototype(new ObjectPrototype(...$nodeListSchema->nodes));

        return $this->nodeBuilder;
    }
}