<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Node\Node;

class ObjectPrototype extends AbstractPrototype
{
    private readonly ?NodeListSchema $nodeListSchema;

    /**
     * @param Node<mixed> ...$node
     */
    public function __construct(
        Node ...$node,
    ) {
        $this->nodeListSchema = [] === $node ? null : new NodeListSchema(...$node);
    }

    #[\Override]
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext,
    ): ValidationResult {
        $validationResult = parent::isValueSatisfiesPrototypeSpecification($values, $validationContext);

        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    sprintf('Expected an object, got %s.', get_debug_type($value))
                );

                return $validationResult;
            }

            if (array_any(array_keys($value), static fn (string|int $subKey) => !is_string($subKey))) {
                $validationResult->addError(
                    $validationContext->getFullPathFromKey($key),
                    'All children should have a key.'
                );

                return $validationResult;
            }

            if (null === $this->nodeListSchema) {
                continue;
            }

            $objectNode = new ObjectNode($validationContext->getFullPathFromKey($key), $this->nodeListSchema);

            $validationResult->merge($objectNode->isValueSatisfiesNodeSpecification(
                $value,
                new ValidationContext()
            ));
        }

        return $validationResult;
    }

    /**
     * @template T of array<string, mixed>|array<int, mixed>
     *
     * @param T $array
     *
     * @return T
     */
    #[\Override]
    public function normalize(array $array): array
    {
        $array = parent::normalize($array);

        if (null === $this->nodeListSchema) {
            return $array;
        }

        /** @var array<string, mixed> $value */
        foreach ($array as $key => $value) {
            $objectNode = new ObjectNode('', $this->nodeListSchema);

            $nodeValue = $objectNode->normalize($value);

            if (null === $nodeValue) {
                unset($array[$key]);
                continue;
            }

            $array[$key] = $nodeValue;
        }

        /** @var T $array needed because phpstan think the type has changed even if it not the case */
        return $array;
    }
}
