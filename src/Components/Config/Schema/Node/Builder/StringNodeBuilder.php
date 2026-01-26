<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Validation\String\LengthConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\String\PatternConstraintValidator;

/**
 * @extends NodeBuilder<string>
 */
class StringNodeBuilder extends NodeBuilder
{
    private bool $isRequired = false;

    private ?string $defaultValue = null;

    public function __construct(
        private readonly string $key,
        private readonly NodeListSchemaBuilder $nodeListSchemaBuilder
    ) {}

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function defaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param string $pattern The regex must have delimiter (# | /) and make sure to escape the "/" by a "\\" e.g.
     * <code>
     *     $builder->pattern('#^https?:\/\/[a-zA-Z0-9_-∖.]+\.\w{2,}(\/[\/\w]+)?$#')
     * </code>
     */
    public function pattern(string $pattern, ?string $message = null): self
    {
        $this->constraints[] = new PatternConstraintValidator($pattern, $message);

        return $this;
    }

    public function length(?int $min, ?int $max): self
    {
        $this->constraints[] = new LengthConstraintValidator($min, $max);

        return $this;
    }

    public function buildNode(): NodeListSchemaBuilder
    {
        $stringNode = new StringNode($this->key);

        if (null !== $this->defaultValue) {
            $stringNode->defaultValue($this->defaultValue);
        }

        if ($this->isRequired) {
            $stringNode->required();
        }

        $this->nodeListSchemaBuilder->add($stringNode);

        return $this->nodeListSchemaBuilder;
    }
}
