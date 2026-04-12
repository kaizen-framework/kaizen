<?php

declare(strict_types=1);

namespace Kaizen\Components\Container;

use Kaizen\Components\Container\Definition\ServiceDefinition;

class ContainerBuilder
{
    /**
     * @var array<string, ServiceDefinition>
     */
    private(set) array $servicesDefinitions = [];

    /**
     * @param class-string $class
     */
    public function register(string $class, ?string $id = null): ServiceDefinition
    {
        $id ??= $class;

        return $this->servicesDefinitions[$id] = new ServiceDefinition($id, $class);
    }
}