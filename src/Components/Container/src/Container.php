<?php

declare(strict_types=1);

namespace Kaizen\Components\Container;

class Container implements ContainerInterface
{
    /** @var array<string, object> */
    private array $services = [];
    /** @var array<string, null|array|bool|string|int|float|\UnitEnum> */
    private array $parameters = [];

    public function get(string $id, bool $throwOnServiceNotFound = false): ?object
    {
        if ($this->has($id)) {
            return $this->services[$id];
        }

        if ($throwOnServiceNotFound) {
            throw new ServiceNotFoundException('The service "' . $id . '" does not exist.');
        }

        return null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    public function set(string $id, object $service): void
    {
        $this->services[$id] = $service;
    }

    public function getParameter(string $name, bool $throwOnNotFoundParameter = false): null|array|bool|string|int|float|\UnitEnum
    {
        return $this->parameters[$name] ?? null;
    }

    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function setParameter(string $name, \UnitEnum|float|int|bool|array|string|null $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Compile the container services and parameters as files
     *
     * @return array{services: \SplFileInfo, parameters: \SplFileInfo}
     */
    public function compileContainer(string $compileLocation): array
    {
        if (!is_dir($compileLocation)) {
            if (!mkdir($compileLocation, 0777, true)) {
                throw new \Exception(error_get_last()['message']); // TODO
            }
        }

        // TODO logic to compile the container

        return [
            'services' => '',// TODO,
            'parameters' => ''// TODO,
        ];
    }
}