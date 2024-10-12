<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Loader;

use Kaizen\Components\Config\Definition\FileConfigDefinition;
use Kaizen\Components\Config\Exception\LoaderException;

class YamlLoader implements ConfigLoaderInterface
{
    public function load(string $path): FileConfigDefinition
    {
        $fileArray = yaml_parse_file(realpath($path));

        if (false === $fileArray) {
            throw new \RuntimeException(
                'Can not load ' . $path . ' config file ensure that the path is correct'
            );
        }

        if (!is_array($fileArray)) {
            throw new LoaderException(sprintf(
                'Your config file "%s" must contains at least 1 "key: value"',
                $path
            ));
        }

        return new FileConfigDefinition($path, $fileArray);
    }

    public function getExtension(): string
    {
        return 'yaml';
    }
}