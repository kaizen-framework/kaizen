<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(BooleanNode::class)]
class BooleanNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new BooleanNode('boolean');
        $node->validateType(true);

        self::assertEquals('boolean', $node->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [123];

        yield ['test'];

        yield [123.123];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $node = new BooleanNode('boolean');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
