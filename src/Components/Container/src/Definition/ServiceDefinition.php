<?php

declare(strict_types=1);

namespace Kaizen\Components\Container\Definition;

class ServiceDefinition
{
    /** @var string[] */
    private(set) array $tags = [];
    /** @var ServiceDefinition[] */
    private(set) array $dependencies;
    private(set) bool $isShared = false;

    public function __construct(
        public string $id,
        public string $class,
    ) { }

    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;

        return $this;
    }
}