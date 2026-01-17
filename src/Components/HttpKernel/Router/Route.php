<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\Router;

use Kaizen\Components\HttpKernel\AbstractController;
use Kaizen\Components\HttpKernel\HttpFoundation\Constant\RequestMethod;

readonly class Route
{
    public function __construct(
        public string $name,
        public string $uri,
        public RequestMethod $method,
        public AbstractController $controller
    ) { }
}