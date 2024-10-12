<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\StringNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(StringNode::class)]
class StringNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new StringNode('string');
        $node->validateType('okok');

        self::assertEquals('string', $node->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];

        yield [123];

        yield [123.22];

        yield [[]];

        yield [new \stdClass()];
    }

    /**
     * @param array<int, mixed>|bool|float|int|object $value
     */
    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $node = new StringNode('string');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
