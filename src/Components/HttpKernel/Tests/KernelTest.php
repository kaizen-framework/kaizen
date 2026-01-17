<?php

namespace Kaizen\Components\HttpKernel\Tests;

use Kaizen\Components\Container\Container;
use Kaizen\Components\HttpKernel\Kernel;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private string $baseDir = __DIR__ . '/Fixtures/Boilerplate';

    public function testExceptionOnInvalidBasePath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Can not initialize %s base path is not valid',
            Kernel::class
        ));
        new Kernel('invalid path');
    }

    public function testConstructKernelWithBasePath(): void
    {
        self::assertArrayNotHasKey('project-root-dir', $_ENV);

        new Kernel($this->baseDir);
        self::assertArrayHasKey('project-root-dir', $_ENV);
    }

    public function testConstructKernelWithoutBasePathButWithEnv(): void
    {
        $this->expectNotToPerformAssertions();
        
        $_ENV['project-root-dir'] = $this->baseDir;
        new Kernel();
    }

    public function testBootDotEnv(): void
    {
        new Kernel($this->baseDir);
        $container = Container::getContainer();

        self::assertEquals('123=', $container->getParameter('VERY_SECRET_KEY'));
        self::assertEquals('test', $container->getParameter('TEST'));
        self::assertEquals('123=', $_ENV['VERY_SECRET_KEY']);
        self::assertEquals('test', $_ENV['TEST']);
    }

    public function testBootConfig(): void
    {
        new Kernel($this->baseDir);
        $container = Container::getContainer();

        self::assertTrue($container->hasParameter('test'));
        self::assertEquals('test', $container->getParameter('test'));
    }
}
