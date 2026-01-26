<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\ScalarNode;

/**
 * @extends NodeBuilder<scalar>
 */
class ScalarNodeBuilder extends NodeBuilder
{
    protected bool $isRequired = false;

    protected null|bool|float|int|string $defaultValue = null;

    public function __construct(
        protected readonly string $key,
        protected readonly NodeListSchemaBuilder $configSchemaBuilder
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(bool|float|int|string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function buildNode(): NodeListSchemaBuilder
    {
        $scalarNode = new ScalarNode($this->key);

        if (null !== $this->defaultValue) {
            $scalarNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $scalarNode->required();
        }

        $this->configSchemaBuilder->add($scalarNode);

        return $this->configSchemaBuilder;
    }
}
