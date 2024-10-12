<?php

namespace Kaizen\Components\Config\Schema\Prototype;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\NodeInterface;

class ObjectPrototype extends AbstractPrototype
{
    private ?ConfigSchema $prototypeSchema;

    /**
     * @param NodeInterface[]|null $nodes
     */
    public function __construct(
        ?NodeInterface ...$nodes,
    ) {
        $this->prototypeSchema = $nodes ? new ConfigSchema(...$nodes) : null;
    }

    public function validatePrototype(array $array): void
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                throw new InvalidNodeTypeException();
            }

            $this->validateObject($item);
        }
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function processPrototype(array $array): array
    {
        foreach ($array as $key => $value) {
            $array[$key] = $this->processPrototypeValue($value);
        }

        return $array;
    }

    /**
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    private function processPrototypeValue(array $array): array
    {
        $this->appendDefaultValueForMissingNodes($array);

        foreach ($array as $key => $value) {
            $node = $this->prototypeSchema->getNode($key);

            $array[$key] = $node->processValue($value);
        }

        return $array;
    }

    /**
     * @throws InvalidNodeTypeException
     */
    private function validateObject(array $object): void
    {
        if (empty($object)) {
            throw new InvalidNodeTypeException('Empty object are not allowed');
        }

        foreach ($object as $key => $value) {
            if (!is_string($key)) {
                throw new InvalidNodeTypeException('must be of type object');
            }

            if (!$this->prototypeSchema) {
                continue;
            }

            $node = $this->prototypeSchema->getNode($key);

            if (!$node) {
                throw new InvalidNodeTypeException(sprintf(
                    'key %s not allowed by the prototype, expected keys "%s"',
                    $key,
                    implode('", "', $this->prototypeSchema->getNodeKeys())
                ));
            }

            $node->validateType($value);
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function appendDefaultValueForMissingNodes(array &$config): void
    {
        foreach ($this->prototypeSchema->getNodes() as $node) {
            if (!array_key_exists($node->getKey(), $config) && null !== $defaultValue = $node->getDefaultValue()) {
                $config[$node->getKey()] = $defaultValue;
            }
        }
    }
}
