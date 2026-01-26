<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\NumericNode;
use Kaizen\Components\Config\Schema\Validation\Numeric\MaxConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\Numeric\MinConstraintValidator;

/**
 * @extends NodeBuilder<int|float>
 */
class NumericNodeBuilder extends NodeBuilder
{
    private null|float|int $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(int|float $value): self
    {
        /** @var MinConstraintValidator<int|float> $minConstraint */
        $minConstraint = new MinConstraintValidator($value);

        $this->constraints[] = $minConstraint;

        return $this;
    }

    public function max(int|float $value): self
    {
        /** @var MaxConstraintValidator<int|float> $maxConstraint */
        $maxConstraint = new MaxConstraintValidator($value);

        $this->constraints[] = $maxConstraint;

        return $this;
    }

    public function defaultValue(float|int $defaultValue): self
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
        $numericNode = new NumericNode($this->key);
        $numericNode->setConstraintValidators($this->constraints);

        if (null !== $this->defaultValue) {
            $numericNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $numericNode->required();
        }

        $this->configSchemaBuilder->add($numericNode);

        return $this->configSchemaBuilder;
    }
}
