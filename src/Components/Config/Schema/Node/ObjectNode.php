<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\ConfigSchema;

class ObjectNode extends Node implements ParentNodeInterface
{
    public function __construct(
        private readonly string $key,
        private readonly ?ConfigSchema $children = null,
    ) {}

    public function validateType(mixed $value): void
    {
        if (!is_array($value)) {
            throw new InvalidNodeTypeException(sprintf(
                'The node "%s" must be of type "object", "%s" given',
                $this->getKey(),
                get_debug_type($value)
            ));
        }

        if (!$this->children) {
            return;
        }

        foreach ($value as $key => $currentValue) {
            $node = $this->children->getNode($key);

            if (!$node) {
                throw new InvalidNodeTypeException(sprintf(
                    'key %s not allowed within the %s node, allowed keys : %s',
                    $key,
                    $this->getKey(),
                    implode(', ', $this->children->getNodeKeys())
                ));
            }

            $node->validateType($currentValue);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getChildren(): ConfigSchema
    {
        return $this->children;
    }
}
