<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\NumericPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(NumericPrototype::class)]
class NumericPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[123, 12.1]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[1, new \stdClass()], 'test[1]', 'Expected an array of numeric values.'];
        yield [['string', 12.12], 'test[0]', 'Expected an array of numeric values.'];
        yield [[true], 'test[0]', 'Expected an array of numeric values.'];
        yield [[1, 'test'], 'test[1]', 'Expected an array of numeric values.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new NumericPrototype();
    }
}
