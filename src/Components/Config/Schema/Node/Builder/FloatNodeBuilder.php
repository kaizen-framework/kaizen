<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Validation\Numeric\MaxConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\Numeric\MinConstraintValidator;

/**
 * @extends NodeBuilder<float>
 */
class FloatNodeBuilder extends NodeBuilder
{
    private ?float $defaultValue = null;
    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(float $value): self
    {
        /** @var MinConstraintValidator<float> $minConstraint */
        $minConstraint = new MinConstraintValidator($value);

        $this->constraints[] = $minConstraint;

        return $this;
    }

    public function max(float $value): self
    {
        /** @var MaxConstraintValidator<float> $maxConstraint */
        $maxConstraint = new MaxConstraintValidator($value);

        $this->constraints[] = $maxConstraint;

        return $this;
    }

    public function defaultValue(float $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function buildNode(): NodeListSchemaBuilder
    {
        $floatNode = new FloatNode($this->key);
        $floatNode->setConstraintValidators($this->constraints);

        if (null !== $this->defaultValue) {
            $floatNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $floatNode->required();
        }

        $this->configSchemaBuilder->add($floatNode);

        return $this->configSchemaBuilder;
    }
}
