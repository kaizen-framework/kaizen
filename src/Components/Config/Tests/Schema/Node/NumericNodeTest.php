<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\NumericNode;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(NumericNode::class)]
class NumericNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [10];
        yield [1e5];
        yield [10.5];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true, 'numeric', 'Expected a numeric value, but got bool.'];
        yield ['test', 'numeric', 'Expected a numeric value, but got string.'];
        yield [[], 'numeric', 'Expected a numeric value, but got array.'];
        yield [new \stdClass(), 'numeric', 'Expected a numeric value, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new NumericNode('numeric');
    }
}
