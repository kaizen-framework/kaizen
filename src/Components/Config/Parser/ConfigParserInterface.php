<?php

namespace App\Components\Config\Parser;

use App\Components\Config\Exception\ParseConfigException;
use App\Components\Config\Schema\ConfigSchemaBuilderInterface;

interface ConfigParserInterface
{
    /**
     * @throws ParseConfigException
     */
    public function parseFromFile(string $path, ?ConfigSchemaBuilderInterface $schema = null): array;
}