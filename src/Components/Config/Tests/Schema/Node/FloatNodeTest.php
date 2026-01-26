<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Node\Node;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(FloatNode::class)]
class FloatNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [1.1];
        yield [12.5];
        yield [1e5];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true, 'float', 'Expected a float, but got bool.'];
        yield ['test', 'float', 'Expected a float, but got string.'];
        yield [123, 'float', 'Expected a float, but got int.'];
        yield [[], 'float', 'Expected a float, but got array.'];
        yield [new \stdClass(), 'float', 'Expected a float, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new FloatNode('float');
    }
}
