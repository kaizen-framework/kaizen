<?php

declare(strict_types=1);

namespace Kaizen\Components\Container\Tests;

use Kaizen\Components\Container\Container;
use Kaizen\Components\Container\ServiceNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    public function testSetAndGetService(): void
    {
        $service = new \stdClass();
        $service->foo = 'bar';

        $container = new Container();

        self::assertFalse($container->has('service'));

        $container->set('service', $service);

        self::assertTrue($container->has('service'));
        self::assertSame($service, $container->get('service'));
    }

    public function testGetNonExistingService(): void
    {
        $container = new Container();

        self::assertFalse($container->has('service'));
        self::assertNull($container->get('service'));

        $this->expectException(ServiceNotFoundException::class);
        $container->get('service', true);
    }

    public function testSetAndGetParameter(): void
    {
        $container = new Container();

        self::assertFalse($container->hasParameter('foo'));

        $container->setParameter('foo', 'bar');

        self::assertTrue($container->hasParameter('foo'));

        self::assertSame('bar', $container->getParameter('foo'));
    }

    public function testCompileContainer(): void
    {
        $container = new Container();

        $container->setParameter('foo', 'val1');
        $container->setParameter('bar', 'val2');
        $container->setParameter('baz', 'val3');

        $service1 = new \stdClass();
        $service1->foo = 'val1';
        $container->set('service1', $service1);

        $service2 = new \stdClass();
        $service2->bar = 'val2';
        $container->set('service2', $service2);

        $service3 = new \stdClass();
        $service3->baz = 'val3';
        $container->set('service3', $service3);

        $compilationPath = sprintf('%s/Fixtures/Var', __DIR__);
        //$this->removeDirectory($compilationPath);

        $files = $container->compileContainer($compilationPath);

        $serviceFile = $files['services'];
        self::assertFileExists($serviceFile->getRealPath());

        $parametersFile = $files['parameters'];
        self::assertFileExists($parametersFile->getRealPath());

    }

    private function removeDirectory(string $fullPath): void
    {
        array_map('unlink', glob(sprintf('%s/*', $fullPath)));
        rmdir($fullPath);
    }
}