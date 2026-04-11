<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Processor\ValueObject;

readonly class ValidationError
{
    public function __construct(
        public string $path,
        public string $message,
    ) {}
}
