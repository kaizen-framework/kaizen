<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Validation\Builder\CompositeValidationBuilder;

/**
 * @extends NodeBuilder<bool>
 */
class BooleanNodeBuilder extends NodeBuilder
{
    private ?bool $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $nodeListSchemaBuilder
    ) {}

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

    /**
     * @return CompositeValidationBuilder<bool, self>
     */
    public function ifTrue(): CompositeValidationBuilder
    {
        /** @var CompositeValidationBuilder<bool, self> $validationBuilder */
        $validationBuilder = new CompositeValidationBuilder(
            $this,
            static fn (bool $value) => true === $value
        );

        return $validationBuilder;
    }

    public function buildNode(): NodeListSchemaBuilder
    {
        $booleanNode = new BooleanNode($this->key);

        if ($this->isRequired) {
            $booleanNode->required();
        }

        if (!is_null($this->defaultValue)) {
            $booleanNode->defaultValue($this->defaultValue);
        }

        $this->nodeListSchemaBuilder->add($booleanNode);

        return $this->nodeListSchemaBuilder;
    }
}
