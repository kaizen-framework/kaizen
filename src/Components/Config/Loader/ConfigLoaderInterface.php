<?php

namespace Kaizen\Components\Config\Loader;

use Kaizen\Components\Config\Definition\FileConfigDefinition;

interface ConfigLoaderInterface
{
    public function load(string $path): FileConfigDefinition;

    public function getExtension(): string;
}
