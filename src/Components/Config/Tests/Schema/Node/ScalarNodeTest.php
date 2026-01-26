<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ScalarNode::class)]
class ScalarNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield ['string'];
        yield [123];
        yield [true];
        yield [123.123];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[], 'scalar', 'Expected a scalar value, but got array.'];
        yield [['key' => 'value'], 'scalar', 'Expected a scalar value, but got array.'];
        yield [['arr'], 'scalar', 'Expected a scalar value, but got array.'];
        yield [new \stdClass(), 'scalar', 'Expected a scalar value, but got stdClass.'];
    }

    protected function getNode(): Node
    {
        return new ScalarNode('scalar');
    }
}
