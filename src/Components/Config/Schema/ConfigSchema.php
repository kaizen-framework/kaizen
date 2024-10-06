<?php

declare(strict_types=1);

namespace App\Components\Config\Schema;

use App\Components\Config\Exception\InvalidSchemaException;
use App\Components\Config\Schema\Node\NodeInterface;

class ConfigSchema
{
    /** @var NodeInterface[] $config */
    private readonly array $schema;

    /**
     * @throws InvalidSchemaException
     */
    public function __construct(
        NodeInterface ...$schema
    ) {
        $this->schema = $schema;

        $this->validateSchema();
    }

    public function getNode(string $key): ?NodeInterface
    {
        $node = current(array_filter($this->schema, static fn(NodeInterface $node) => $node->getKey() === $key));

        if (!$node) {
            $node = current(array_filter($this->schema, static fn(NodeInterface $node) => $node->getKey() === '*'));
        }

        return $node ?: null;
    }

    /**
     * @return string[]
     */
    public function getNodeKeys(): array
    {
        return array_map(static fn(NodeInterface $child) => $child->getKey(), $this->schema);
    }

    /**
     * @return NodeInterface[]
     */
    public function getNodes(): array
    {
        return $this->schema;
    }

    /**
     * Perform pre-checks validations for the schema
     * 
     * @throws InvalidSchemaException
     */
    private function validateSchema(): void
    {
        $this->checkForDuplicatedKeys();
    }

    /**
     * @throws InvalidSchemaException
     */
    private function checkForDuplicatedKeys(): void
    {
        $keys = array_map(static fn(NodeInterface $node) => $node->getKey(), $this->schema);

        if (count($keys) !== count($uniqueKeys = array_unique($keys))) {
            throw new InvalidSchemaException(sprintf(
                'Duplicated config keys "%s"',
                implode('", "', $uniqueKeys)
            ));
        }
    }
}