<?php

declare(strict_types=1);

namespace App\Components\Config\Processor;

use App\Components\Config\Exception\ConfigProcessingException;
use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Exception\NodeNotFoundException;
use App\Components\Config\Schema\ConfigSchema;
use App\Components\Config\Schema\Node\ParentNodeInterface;

class ConfigProcessor
{
    /**
     * @throws InvalidNodeTypeException
     * @throws NodeNotFoundException
     * @throws ConfigProcessingException
     */
    public function processConfig(array &$config, ConfigSchema $schema): void
    {
        $this->assertRequiredNodeArePresent($schema, $config);
        $this->appendDefaultValueForMissingNodes($config, $schema);

        foreach ($config as $key => $value) {
            $node = $schema->getNode($key);

            if (!$node) {
                throw new NodeNotFoundException($key);
            }

            if ($node instanceof ParentNodeInterface) {
                $this->processConfig($value, $node->getChildren());

                continue;
            }

            $value = $node->processValue($value);
            $node->validateType($value);
        }
    }

    private function appendDefaultValueForMissingNodes(array &$config, ConfigSchema $schema): void
    {
        foreach ($schema->getNodes() as $node) {
            if (!array_key_exists($node->getKey(), $config) && null !== $defaultValue = $node->getDefaultValue()) {
                $config[$node->getKey()] = $defaultValue;
            }
        }
    }

    /**
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