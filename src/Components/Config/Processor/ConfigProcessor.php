<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\ObjectNode;

class ConfigProcessor
{
    /**
     * Process the configuration against the provided node list schema.
     *   - Replace the default value for the not provided nodes.
     *   - Throw an exception if a misconfiguration is detected.
     *   - Run the provided validations
     *
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     *
     * @throws ConfigProcessingException
     */
    public function processConfig(array $config, ConfigInterface $configInterface): array
    {
        $nodeListSchema = $configInterface->getConfigSchema();
        $this->checkForDuplicatedKeys($nodeListSchema);

        $validationContext = new ValidationContext();

        foreach ($config as $key => $value) {
            $node = $nodeListSchema->getNode($key);

            if (!$node instanceof Node) {
                $errorMessage = sprintf(
                    'Invalid configuration key "%s", allowed keys are: ["%s"].',
                    $key,
                    implode('", "', $nodeListSchema->getNodeKeys())
                );

                if ($potentialKey = $nodeListSchema->getKeyFromLevenshtein($key, $config)) {
                    $errorMessage .= sprintf(' Did you mean "%s" ?', $potentialKey);
                }

                throw new ConfigProcessingException($errorMessage);
            }

            $nodeValidationResult = $node->isValueSatisfiesNodeSpecification($value, $validationContext);

            if (!$nodeValidationResult->isValid()) {
                throw new ConfigProcessingException(sprintf(
                    'Invalid configuration at "%s": %s',
                    $nodeValidationResult->errors[0]->path,
                    $nodeValidationResult->errors[0]->message,
                ));
            }
        }

        foreach ($nodeListSchema->nodes as $node) {
            $nodeValue = $node->normalize($config[$node->key] ?? null);

            if (null === $nodeValue) {
                continue;
            }

            $config[$node->key] = $nodeValue;

            $constraintValidationResult = $node->validateConstraints($nodeValue, $nodeListSchema, $validationContext);

            if (!$constraintValidationResult->isValid()) {
                throw new ConfigProcessingException(sprintf(
                    'Invalid configuration at "%s": %s',
                    $constraintValidationResult->errors[0]->path,
                    $constraintValidationResult->errors[0]->message,
                ));
            }
        }

        $this->assertNoMissingRequiredKeys($config, $nodeListSchema, $validationContext);

        return $config;
    }

    /**
     * @param array<string, mixed> $values
     *
     * @throws ConfigProcessingException
     */
    private function assertNoMissingRequiredKeys(
        array $values,
        NodeListSchema $nodeListSchema,
        ValidationContext $validationContext
    ): void {
        foreach ($nodeListSchema->nodes as $node) {
            if ((!array_key_exists($node->key, $values) || null === $values[$node->key]) && $node->isRequired) {
                throw new ConfigProcessingException(sprintf(
                    'Invalid configuration at "%s": The value is required but was not provided or is null.',
                    $validationContext->getFullPathFromKey($node->key)
                ));
            }
        }
    }

    /**
     * @throws ConfigProcessingException
     */
    private function checkForDuplicatedKeys(NodeListSchema $nodeListSchema): void
    {
        $duplicatedKeys = $this->getDuplicatedKeys($nodeListSchema);
        if ([] !== $duplicatedKeys) {
            throw new ConfigProcessingException(sprintf(
                'The provided configuration is invalid, the keys ["%s"] are duplicated.',
                implode('", "', $duplicatedKeys)
            ));
        }
    }

    /**
     * @param string[] $duplicatedKeys
     *
     * @return string[]
     */
    private function getDuplicatedKeys(
        NodeListSchema $nodeListSchema,
        array $duplicatedKeys = [],
        ?string $configPathPrefix = null
    ): array {
        $keys = $nodeListSchema->getNodeKeys();
        $unique = array_unique($keys);

        if (count($unique) !== count($keys)) {
            $duplicatedKeys = [
                ...$duplicatedKeys,
                ...array_map(
                    fn (string $key) => $this->buildConfigPathPrefix($configPathPrefix, $key),
                    array_filter($keys, static function (string $key) use (&$unique) {
                        $uniqueIndex = array_search($key, $unique);

                        if (false === $uniqueIndex) {
                            return true;
                        }

                        unset($unique[$uniqueIndex]);

                        return false;
                    })
                )
            ];
        }

        /** @var ObjectNode[] $objectNodes */
        $objectNodes = array_filter($nodeListSchema->nodes, static fn (Node $node) => $node instanceof ObjectNode);

        foreach ($objectNodes as $objectNode) {
            $duplicatedKeys = $this->getDuplicatedKeys(
                $objectNode->nodeListSchema,
                $duplicatedKeys,
                $this->buildConfigPathPrefix($configPathPrefix, $objectNode->key)
            );
        }

        return $duplicatedKeys;
    }

    private function buildConfigPathPrefix(?string $pathPrefix, string $currentPath): string
    {
        if (null === $pathPrefix) {
            return $currentPath;
        }

        return sprintf('%s.%s', $pathPrefix, $currentPath);
    }
}
