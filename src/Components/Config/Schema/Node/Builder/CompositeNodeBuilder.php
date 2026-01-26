<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\Builder\PrototypeBuilder;

/**
 * @extends NodeBuilder<array<int|string, mixed>>
 */
abstract class CompositeNodeBuilder extends NodeBuilder
{
    protected ?AbstractPrototype $prototype = null;

    public function setPrototype(AbstractPrototype $prototype): self
    {
        $this->prototype = $prototype;

        return $this;
    }

    public function withPrototype(): PrototypeBuilder
    {
        return new PrototypeBuilder($this);
    }
}