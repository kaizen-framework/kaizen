<?php

declare(strict_types=1);

namespace Kaizen\Components\Container\Tests;

use Kaizen\Components\Container\Tests\Fixtures\Services\ServiceWithoutDependencies;
use PHPUnit\Framework\TestCase;
use Kaizen\Components\Container\ContainerBuilder;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ContainerBuilder::class)]
class ContainerBuilderTest extends TestCase
{
    public function testRegister(): void
    {
        $containerBuilder = new ContainerBuilder();
        $definition = $containerBuilder->register(ServiceWithoutDependencies::class);
        self::assertEquals(ServiceWithoutDependencies::class, $definition->class);
        self::assertEquals(ServiceWithoutDependencies::class, $definition->id);

        $definitions = $containerBuilder->servicesDefinitions;

        self::assertCount(1, $definitions);
        $actualDefinition = $definitions[ServiceWithoutDependencies::class];
        self::assertEquals(ServiceWithoutDependencies::class, $actualDefinition->class);
        self::assertEquals(ServiceWithoutDependencies::class, $actualDefinition->id);
    }

    public function testRegisterWithId(): void
    {
        $containerBuilder = new ContainerBuilder();
        $definition = $containerBuilder->register(ServiceWithoutDependencies::class, 'a');
        self::assertEquals(ServiceWithoutDependencies::class, $definition->class);
        self::assertEquals('a', $definition->id);

        $definitions = $containerBuilder->servicesDefinitions;

        self::assertCount(1, $definitions);
        $actualDefinition = $definitions['a'];
        self::assertEquals(ServiceWithoutDependencies::class, $actualDefinition->class);
        self::assertEquals('a', $actualDefinition->id);
    }
}