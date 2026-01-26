<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\NodeListSchema;

/**
 * @extends Node<array<string, mixed>>
 */
class ObjectNode extends Node
{
    public function __construct(
        string $key,
        public private(set) readonly NodeListSchema $nodeListSchema,
    ) {
        parent::__construct($key);
    }

    #[\Override]
    public function isValueSatisfiesNodeSpecification(
        mixed $value,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesNodeSpecification($value, $validationContext);

        if (!$validationResult->isValid() || null === $value) {
            return $validationResult;
        }

        if (!is_array($value)) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                sprintf(
                    'Expected a node with children, but got %s.',
                    get_debug_type($value)
                )
            );

            return $validationResult;
        }

        if (array_any(array_keys($value), static fn (string|int $key) => !is_string($key))) {
            $validationResult->addError(
                $validationContext->getFullPathFromKey($this->key),
                'All children should have a key.'
            );

            return $validationResult;
        }

        /** @var array<string, mixed> $value */
        $validationResult->merge($this->checkIfRequiredNodesAreMissing($value, $validationContext->child($this->key)));

        foreach ($value as $key => $currentValue) {
            $node = $this->nodeListSchema->getNode($key);

            if (null === $node) {
                $errorMessage = sprintf(
                    'Unknown "%s", allowed keys are: ["%s"].',
                    $key,
                    implode('", "', $this->nodeListSchema->getNodeKeys())
                );

                if ($potentialKey = $this->nodeListSchema->getKeyFromLevenshtein($key, $value)) {
                    $errorMessage .= sprintf(' Did you mean "%s" ?', $potentialKey);
                }

                $validationResult->addError($validationContext->getFullPathFromKey($this->key), $errorMessage);

                return $validationResult;
            }

            $validationResult->merge($node->isValueSatisfiesNodeSpecification(
                $currentValue,
                $validationContext->child($this->key)
            ));
        }

        return $validationResult;
    }

    #[\Override]
    public function normalize(mixed $value): mixed
    {
        $value = parent::normalize($value);

        if (null === $value) {
            return $value;
        }

        foreach ($this->nodeListSchema->nodes as $node) {
            $nodeValue = $node->normalize($value[$node->key] ?? null);

            if (null === $nodeValue) {
                continue;
            }

            $value[$node->key] = $nodeValue;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $values
     */
    private function checkIfRequiredNodesAreMissing(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = new ValidationResult();

        foreach ($this->nodeListSchema->nodes as $node) {
            if ((!array_key_exists($node->key, $values) || null === $values[$node->key]) && $node->isRequired) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($node->key),
                    'The value is required but was not provided or is null.'
                );
            }
        }

        return $validationResult;
    }

    #[\Override]
    public function validateConstraints(
        mixed $value,
        NodeListSchema $nodeListSchema,
        ValidationContext $validationContext
    ): ValidationResult {
        $validationResult = parent::validateConstraints($value, $nodeListSchema, $validationContext);

        foreach ($value as $key => $item) {
            /** @var Node<mixed> $node */
            $node = $this->nodeListSchema->getNode($key);

            $validationResult->merge($node->validateConstraints($item, $nodeListSchema, $validationContext->child($this->key)));
        }

        return $validationResult;
    }
}
