<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor;

readonly class ValidationContext
{
    public function __construct(
        public ?string $path = null
    ) {}

    public function getFullPathFromKey(string|int $key): string
    {
        if (is_int($key)) {
            return $this->path ? sprintf('%s[%s]', $this->path, $key) : (string) $key;
        }

        return $this->path ? sprintf('%s.%s', $this->path, $key) : $key;
    }

    public function child(string|int $childKey): self
    {
        return new self($this->getFullPathFromKey($childKey));
    }
}