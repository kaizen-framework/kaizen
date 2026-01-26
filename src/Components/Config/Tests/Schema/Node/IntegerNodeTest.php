<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\Node;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(IntegerNode::class)]
class IntegerNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [1];
        yield [10];
        yield [155];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true, 'integer', 'Expected an int, but got bool.'];
        yield ['test', 'integer', 'Expected an int, but got string.'];
        yield [123.123, 'integer', 'Expected an int, but got float.'];
        yield [1e7, 'integer', 'Expected an int, but got float.'];
        yield [[], 'integer', 'Expected an int, but got array.'];
        yield [new \stdClass(), 'integer', 'Expected an int, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new IntegerNode('integer');
    }
}
