<?php

namespace App\Components\Config\Processor;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Exception\NodeNotFoundException;
use App\Components\Config\Schema\Node\NodeInterface;

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