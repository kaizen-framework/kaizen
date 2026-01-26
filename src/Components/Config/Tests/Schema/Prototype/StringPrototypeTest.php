<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(StringPrototype::class)]
class StringPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [['string']];
        yield [['string1', 'string2']];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[new \stdClass()], 'test[0]', 'Expected an array of string.'];
        yield [[[1,2,3]], 'test[0]', 'Expected an array of string.'];
        yield [[['key' => 'value']], 'test[0]', 'Expected an array of string.'];
        yield [[null], 'test[0]', 'Expected an array of string.'];
        yield [[[]], 'test[0]', 'Expected an array of string.'];
        yield [['string', 12], 'test[1]', 'Expected an array of string.'];
        yield [[false, 'string'], 'test[0]', 'Expected an array of string.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new StringPrototype();
    }
}
