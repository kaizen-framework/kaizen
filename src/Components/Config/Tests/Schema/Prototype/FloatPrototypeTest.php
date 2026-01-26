<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\FloatPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(FloatPrototype::class)]
class FloatPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[123.12, 12.0, 1e10]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[1, new \stdClass()], 'test[0]', 'Expected an array of float.'];
        yield [['string'], 'test[0]', 'Expected an array of float.'];
        yield [[true, 1.1], 'test[0]', 'Expected an array of float.'];
        yield [[1.1, 'test'], 'test[1]', 'Expected an array of float.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new FloatPrototype();
    }
}
