<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Validation\Numeric\MaxConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\Numeric\MinConstraintValidator;

/**
 * @extends NodeBuilder<int>
 */
class IntegerNodeBuilder extends NodeBuilder
{
    private ?int $defaultValue = null;

    private bool $isRequired = false;

    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $configSchemaBuilder
    ) {}

    public function min(int $value): self
    {
        /** @var MinConstraintValidator<int> $minConstraint */
        $minConstraint = new MinConstraintValidator($value);

        $this->constraints[] = $minConstraint;

        return $this;
    }

    public function max(int $value): self
    {
        /** @var MaxConstraintValidator<int> $maxConstraint */
        $maxConstraint = new MaxConstraintValidator($value);

        $this->constraints[] = $maxConstraint;

        return $this;
    }

    public function defaultValue(int $defaultValue): self
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
        $integerNode = new IntegerNode($this->key);
        $integerNode->setConstraintValidators($this->constraints);

        if (null !== $this->defaultValue) {
            $integerNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $integerNode->required();
        }

        $this->configSchemaBuilder->add($integerNode);

        return $this->configSchemaBuilder;
    }
}
