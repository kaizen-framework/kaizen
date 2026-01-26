<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Schema;

use Kaizen\Components\Config\Schema\Node\Node;

class NodeListSchema
{
    /** @var Node<mixed>[] */
    public private(set) array $nodes;

    /**
     * @param Node<mixed> ...$nodes
     */
    public function __construct(
        Node ...$nodes
    ) {
        $this->nodes = $nodes;
    }

    /**
     * @return null|Node<mixed>
     */
    public function getNode(string $key): ?Node
    {
        return array_find($this->nodes, static fn (Node $node) => $node->key === $key);
    }

    /**
     * @return string[]
     */
    public function getNodeKeys(): array
    {
        return array_map(static fn (Node $node) => $node->key, $this->nodes);
    }

    /**
     * @param array<string, mixed> $actualConfig
     */
    public function getKeyFromLevenshtein(string $key, array $actualConfig, int $maxDistanceToCompare = 4): ?string
    {
        $occurrences = [];

        foreach ($this->getNodeKeys() as $nodeKey) {
            if (array_key_exists($nodeKey, $actualConfig)) {
                continue;
            }

            $computedDistance = levenshtein($key, $nodeKey, 1, 1, 2);

            if ($computedDistance <= $maxDistanceToCompare) {
                $occurrences[] = ['key' => $nodeKey, 'distance' => $computedDistance];
            }
        }

        if ([] === $occurrences) {
            return null;
        }

        usort($occurrences, static fn(array $a, array $b) => $a['distance'] <=> $b['distance']);

        return $occurrences[0]['key'];
    }
}
