<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Kaizen\Components\Config\Processor\ValidationContext;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ValidationContext::class)]
class ValidationContextTest extends TestCase
{
    public function testGetPathFromKey(): void
    {
        $validationContext = new ValidationContext('node1');

        self::assertSame('node1.node2', $validationContext->getFullPathFromKey('node2'));
        self::assertSame('node1[0]', $validationContext->getFullPathFromKey(0));
    }

    public function testChild(): void
    {
        $validationContext = new ValidationContext('node1');

        $childContext = $validationContext->child(0);

        self::assertNotSame($validationContext, $childContext);
        self::assertSame('node1[0]', $childContext->path);
    }
}