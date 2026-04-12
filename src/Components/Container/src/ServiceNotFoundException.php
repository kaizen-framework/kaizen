<?php

declare(strict_types=1);

namespace Kaizen\Components\Container;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}