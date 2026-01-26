<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;

/**
 * @template-covariant T
 */
abstract class Node
{
    public private(set) bool $isRequired = false;
    /** @var T|null $defaultValue */
    public protected(set) mixed $defaultValue = null;
    /** @var NodeConstraintValidatorInterface<T>[] */
    public private(set) array $constraintValidators = [];

    public function __construct(
        public readonly string $key,
    ) {}

    /**
     * @param NodeConstraintValidatorInterface<T> $constraintValidator
     */
    public function addConstraintValidator(NodeConstraintValidatorInterface $constraintValidator): static
    {
        $this->constraintValidators[] = $constraintValidator;

        return $this;
    }

    /**
     * @param NodeConstraintValidatorInterface<T>[] $constraintValidators
     */
    public function setConstraintValidators(array $constraintValidators): static
    {
        $this->constraintValidators = $constraintValidators;

        return $this;
    }

    public function required(): static
    {
        $this->isRequired = true;

        return $this;
    }

    /**
     * @param T|null $defaultValue
     */
    public function defaultValue(mixed $defaultValue): static
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Apply transformations on a value based on the node specifications, then return the value.
     *
     * @param T|null $value
     *
     * @return T|null
     */
    public function normalize(mixed $value): mixed
    {
        if (null === $value && null !== $this->defaultValue) {
            return $this->defaultValue;
        }

        return $value;
    }

    /**
     * Validate the type of the node.
     */
    public function isValueSatisfiesNodeSpecification(
        mixed $value,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = new ValidationResult();

        if (null === $value && $this->isRequired) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                'The value is required but was not provided or is null.'
            );
        }

        return $validationResult;
    }

    /**
     * @param T $value
     */
    public function validateConstraints(
        mixed $value,
        NodeListSchema $nodeListSchema,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = new ValidationResult();

        foreach ($this->constraintValidators as $constraintValidator) {
            if (!$constraintValidator->validate($value, $this, $nodeListSchema)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($this->key),
                    $constraintValidator->getMessage()
                );
            }
        }

        return $validationResult;
    }
}
