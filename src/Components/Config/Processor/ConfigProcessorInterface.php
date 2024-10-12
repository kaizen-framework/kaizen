<?php

namespace Kaizen\Components\Config\Processor;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Exception\NodeNotFoundException;
use Kaizen\Components\Config\Schema\Node\NodeInterface;

interface ConfigProcessorInterface
{
    /**
     * Validate the configuration throw an exception if the configuration not match the defined schema
     *
     * @param NodeInterface[] $schema
     *
     * @throws NodeNotFoundException
     * @throws InvalidNodeTypeException
     */
    public function processConfig(array $config, array $schema): void;
}