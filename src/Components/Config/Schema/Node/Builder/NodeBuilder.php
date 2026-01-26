<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node\Builder;

use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Validation\Builder\CompositeValidationBuilder;
use Kaizen\Components\Config\Schema\Validation\ClosureConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;

/**
 * @template T
 */
abstract class NodeBuilder
{
    /**
     * @var NodeConstraintValidatorInterface<T>[]
     */
    protected array $constraints = [];

    abstract public function buildNode(): NodeListSchemaBuilder;

    /**
     * @param \Closure(T): bool $closure
     */
    public function validateClosure(\Closure $closure, string $message): static
    {
        /** @var ClosureConstraintValidator<T> $constraint */
        $constraint = new ClosureConstraintValidator($closure, $message);

        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @param \Closure(T): bool $when
     *
     * @return CompositeValidationBuilder<T, $this>
     */
    public function when(\Closure $when): CompositeValidationBuilder
    {
        /** @var CompositeValidationBuilder<T, $this> $compositeValidationBuilder */
        $compositeValidationBuilder = new CompositeValidationBuilder($this, $when);

        return $compositeValidationBuilder;
    }

    /**
     * @param NodeConstraintValidatorInterface<T> $nodeConstraintValidator
     */
    public function customValidation(NodeConstraintValidatorInterface $nodeConstraintValidator): static
    {
        $this->constraints[] = $nodeConstraintValidator;

        return $this;
    }
}
