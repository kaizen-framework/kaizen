<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\IntegerPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(IntegerPrototype::class)]
class IntegerPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[123, 12]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[1, new \stdClass()], 'test[1]', 'Expected an array of integers.'];
        yield [[12.12], 'test[0]', 'Expected an array of integers.'];
        yield [['string'], 'test[0]', 'Expected an array of integers.'];
        yield [[true], 'test[0]', 'Expected an array of integers.'];
        yield [[true, 'test'], 'test[0]', 'Expected an array of integers.'];
        yield [[1, 'test'], 'test[1]', 'Expected an array of integers.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new IntegerPrototype();
    }
}
