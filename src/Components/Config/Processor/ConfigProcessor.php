<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Exception\NodeNotFoundException;
use Kaizen\Components\Config\Schema\ConfigSchema;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\ParentNodeInterface;

class ConfigProcessor
{
    /**
     * @param array<string, mixed> $config
     *
     * @throws InvalidNodeTypeException
     * @throws NodeNotFoundException
     * @throws ConfigProcessingException
     */
    public function processConfig(array &$config, ConfigInterface $configInterface): void
    {
        $schema = $configInterface->getConfigSchema();

        $config = $this->doProcess($config, $schema);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     *
     * @throws InvalidNodeTypeException
     * @throws NodeNotFoundException
     * @throws ConfigProcessingException
     */
    private function doProcess(array $config, ConfigSchema $schema): array
    {
        $this->assertRequiredNodeArePresent($schema, $config);
        $this->appendDefaultValueForMissingNodes($config, $schema);

        foreach ($config as $key => $value) {
            $node = $schema->getNode($key);

            if (!$node) {
                throw new NodeNotFoundException($key);
            }

            if ($node instanceof ParentNodeInterface) {
                $config[$key] = $this->doProcess($value, $node->getChildren());

                continue;
            }

            $config[$key] = $node->processValue($value);
            $node->validateType($value);
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function appendDefaultValueForMissingNodes(array &$config, ConfigSchema $schema): void
    {
        foreach ($schema->getNodes() as $node) {
            if (!array_key_exists($node->getKey(), $config) && null !== $defaultValue = $node->getDefaultValue()) {
                $config[$node->getKey()] = $defaultValue;
            }
        }
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws ConfigProcessingException
     */
    private function assertRequiredNodeArePresent(ConfigSchema $schema, array $config): void
    {
        foreach ($schema->getNodes() as $node) {
            if ($node->isRequired() && !array_key_exists($node->getKey(), $config)) {
                throw new ConfigProcessingException(sprintf(
                    'Required config parameter "%s" is missing.',
                    $node->getKey()
                ));
            }
        }
    }
}