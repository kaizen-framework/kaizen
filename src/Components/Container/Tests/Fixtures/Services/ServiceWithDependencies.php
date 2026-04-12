<?php

declare(strict_types=1);

namespace Kaizen\Components\Container\Tests\Fixtures\Services;

readonly class ServiceWithDependencies
{
    public function __construct(
        private ServiceWithoutDependencies $serviceWithoutDependencies
    ) { }
}