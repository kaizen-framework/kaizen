<?php

namespace Kaizen\Components\HttpKernel\HttpFoundation\Bag;

class ParameterBag
{
    /**
     * @param array<string,mixed> $parameters
     */
    public function __construct(
        private array $parameters = []
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->parameters[$key] : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function add(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->parameters;
    }
}