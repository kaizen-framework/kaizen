<?php

namespace Kaizen\Components\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * @inheritDoc
     *
     * @template Class of object
     *
     * @param class-string<Class>|string $id ID in the container of the service you looking for
     * @param bool $throwOnServiceNotFound By default, the container will return null if the service is not found, however you can set this parameter to true to throw an exception if the service is not found
     *
     * @return ($id is class-string<Class>
     *     ? ($throwOnServiceNotFound is true ? Class : null|Class)
     *     : ($throwOnServiceNotFound is true ? object : null|object)
     * )
     *
     * @throws ServiceNotFoundException Thrown if the parameter $throwOnServiceNotFound is true and the service is not found
     */
    public function get(string $id, bool $throwOnServiceNotFound = false): ?object;

    /**
     * Check if a service is into the container
     */
    public function has(string $id): bool;

    /**
     * Add a service into the container
     */
    public function set(string $id, object $service): void;

    /**
     * Get a parameter into the container
     *
     * @return array<string|int, mixed>|bool|string|int|float|\UnitEnum|null
     */
    public function getParameter(
        string $name,
        bool $throwOnNotFoundParameter = false
    ): null|array|bool|string|int|float|\UnitEnum;

    /**
     * Check if a parameter is into the container
     */
    public function hasParameter(string $name): bool;

    /**
     * Add a parameter into the container
     *
     * @param array<string|int, mixed>|bool|string|int|float|\UnitEnum|null $value
     */
    public function setParameter(
        string $name,
        null|array|bool|string|int|float|\UnitEnum $value
    ): void;
}