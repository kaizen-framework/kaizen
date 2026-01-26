<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\TuplePrototype;
use Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(TuplePrototype::class)]
class TuplePrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [['test', 123, 1.21, false, 'string']];
        yield [['test2', 12, 10.5, false, 12]];
        yield [['test2', 12, 10.5, false, 12.12]];
        yield [['test2', 12, 1e5, true, false]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[123], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [['test', 123], 'test[1]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [[123, 'test', true], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [[true, 'test', true], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [[12, 23.32, true], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [['test', 123, 11.11, 'not expected'], 'test[3]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [['test', 123.123, false], 'test[1]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [[123, 'string', false], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [['string', false, 123], 'test[1]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [[12, 'test', 10.5, true, false], 'test[0]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
        yield [['test', 10.5, 10, true, false], 'test[1]', 'Expected a tuple array of [string, int, float, bool, scalar].'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::FLOAT,
            TupleTypesEnum::BOOLEAN,
            TupleTypesEnum::SCALAR,
        );
    }
}
