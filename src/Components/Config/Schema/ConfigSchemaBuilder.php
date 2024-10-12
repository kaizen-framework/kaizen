<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema;

use Kaizen\Components\Config\Exception\InvalidSchemaException;
use Kaizen\Components\Config\Schema\Node\Builder\ArrayNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\BooleanNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\FloatNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\IntegerNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\ObjectVariableNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\ScalarNodeBuilder;
use Kaizen\Components\Config\Schema\Node\Builder\StringNodeBuilder;
use Kaizen\Components\Config\Schema\Node\NodeInterface;
use Kaizen\Components\Config\Schema\Node\ObjectNode;

class ConfigSchemaBuilder
{
    /** @var NodeInterface[] */
    private array $nodes = [];

    public function __construct(
        private readonly ?self $parent = null,
        private readonly ?string $key = null,
    ) {}

    public function integer(string $key): IntegerNodeBuilder
    {
        return new IntegerNodeBuilder($key, $this);
    }

    public function float(string $key): FloatNodeBuilder
    {
        return new FloatNodeBuilder($key, $this);
    }

    public function string(string $key): StringNodeBuilder
    {
        return new StringNodeBuilder($key, $this);
    }

    public function scalar(string $key): ScalarNodeBuilder
    {
        return new ScalarNodeBuilder($key, $this);
    }

    public function objectVariable(string $key): ObjectVariableNodeBuilder
    {
        return new ObjectVariableNodeBuilder($key, $this);
    }

    public function boolean(string $key): BooleanNodeBuilder
    {
        return new BooleanNodeBuilder($key, $this);
    }

    public function array(string $key): ArrayNodeBuilder
    {
        return new ArrayNodeBuilder($key, $this);
    }

    public function child(string $key): self
    {
        return new self($this, $key);
    }

    public function add(NodeInterface $node): void
    {
        $this->nodes[] = $node;
    }

    /**
     * @throws InvalidSchemaException
     */
    public function build(): ConfigSchema|self
    {
        $schema = new ConfigSchema(...$this->nodes);

        if ($this->parent) {
            $this->parent->add(new ObjectNode($this->key, $schema));

            return $this->parent;
        }

        return $schema;
    }
}
