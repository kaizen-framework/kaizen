<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel;

use Kaizen\Components\HttpKernel\HttpFoundation\Response;

abstract class AbstractController
{
    abstract function __invoke(): Response;
}
