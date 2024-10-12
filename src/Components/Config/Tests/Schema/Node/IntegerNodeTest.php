<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(IntegerNode::class)]
class IntegerNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new IntegerNode('integer');
        $node->validateType(123);

        self::assertEquals('integer', $node->getKey());
    }

    public function testProcessValue(): void
    {
        $node = new IntegerNode('int');
        $node->min(10);

        self::expectException(ConfigProcessingException::class);
        $node->processValue(1);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];

        yield ['test'];

        yield [123.123];

        yield [[]];

        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $node = new IntegerNode('integer');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
