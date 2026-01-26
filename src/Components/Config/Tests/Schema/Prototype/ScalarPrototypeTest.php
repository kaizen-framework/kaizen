<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\ScalarPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ScalarPrototype::class)]
class ScalarPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [['string', true, 123, 11.11]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[new \stdClass()], 'test[0]', 'Expected an array of scalar values.'];
        yield [[[1,2,3]], 'test[0]', 'Expected an array of scalar values.'];
        yield [[['key' => 'value']], 'test[0]', 'Expected an array of scalar values.'];
        yield [[null], 'test[0]', 'Expected an array of scalar values.'];
        yield [[[]], 'test[0]', 'Expected an array of scalar values.'];
        yield [[1, 'string', true, false, []], 'test[4]', 'Expected an array of scalar values.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new ScalarPrototype();
    }
}
