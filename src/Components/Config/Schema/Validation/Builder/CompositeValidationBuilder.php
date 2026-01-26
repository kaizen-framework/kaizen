<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Validation\Builder;

use Kaizen\Components\Config\Schema\Node\Builder\NodeBuilder;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\CompositeValidator;

/**
 * @template T
 * @template TBuilder of NodeBuilder<T>
 */
readonly class CompositeValidationBuilder
{
    /**
     * @param TBuilder $builder
     */
    public function __construct(
        private NodeBuilder $builder,
        private \Closure $when
    ) { }

    /**
     * @return TBuilder
     */
    public function thenFail(string $message): NodeBuilder
    {
        /** @var CompositeValidator<T> $compositeValidator */
        $compositeValidator = new CompositeValidator(
            $this->when,
            false,
            $message
        );

        $this->builder->customValidation($compositeValidator);

        return $this->builder;
    }

    /**
     * @param \Closure(T, NodeListSchema): bool $callback
     *
     * @return TBuilder
     */
    public function then(\Closure $callback, string $message): NodeBuilder
    {
        /** @var CompositeValidator<T> $compositeValidator */
        $compositeValidator = new CompositeValidator(
            $this->when,
            $callback,
            $message
        );

        $this->builder->customValidation($compositeValidator);

        return $this->builder;
    }
}