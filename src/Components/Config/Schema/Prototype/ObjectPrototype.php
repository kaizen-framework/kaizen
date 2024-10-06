<?php

namespace App\Components\Config\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\ConfigSchema;
use App\Components\Config\Schema\Node\NodeInterface;
use App\Components\Config\Schema\NodeFinder;

class ObjectPrototype implements ConfigPrototypeInterface
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
}
