<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\StringNode;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(StringNode::class)]
class StringNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [null];
        yield ['foo'];
        yield ['bar'];
        yield ['baz'];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true, 'string', 'Expected a string, but got bool.'];
        yield [123, 'string', 'Expected a string, but got int.'];
        yield [123.22, 'string', 'Expected a string, but got float.'];
        yield [[], 'string', 'Expected a string, but got array.'];
        yield [new \stdClass(), 'string', 'Expected a string, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new StringNode('string');
    }
}
