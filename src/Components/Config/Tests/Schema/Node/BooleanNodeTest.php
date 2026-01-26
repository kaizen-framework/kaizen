<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\Node;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(BooleanNode::class)]
class BooleanNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [true];
        yield [false];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [123, 'node', 'Expected a boolean, but got int.'];
        yield ['test', 'node', 'Expected a boolean, but got string.'];
        yield [123.123, 'node', 'Expected a boolean, but got float.'];
        yield [[], 'node', 'Expected a boolean, but got array.'];
        yield [new \stdClass(), 'node', 'Expected a boolean, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new BooleanNode('node');
    }
}
