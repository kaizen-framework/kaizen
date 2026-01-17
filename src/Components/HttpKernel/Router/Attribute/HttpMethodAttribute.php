<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\Router\Attribute;

abstract readonly class HttpMethodAttribute
{
    public function __construct(
        public string $name,
        public string $uri
    ) { }
}