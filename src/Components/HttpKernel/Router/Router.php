<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\Router;

class Router
{
    /** @var Route[] */
    private array $routes = [];

    public function registerRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function getRouteByName(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($name === $route->name) {
                return $route;
            }
        }

        return null;
    }

    public function getRouteByUri(string $uri): ?Route
    {
        foreach ($this->routes as $route) {
            if ($uri === $route->uri) {
                return $route;
            }
        }

        return null;
    }
}
