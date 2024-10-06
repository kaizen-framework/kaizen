<?php

namespace App\Components\Config\Tests\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\TestCase;

class StringPrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $stringPrototype = new StringPrototype();

        $stringPrototype->validatePrototype(['test', 'test2', 'test3']);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $stringArray = new ArrayNode('string', new StringPrototype());

        $stringArray->validateType(['test', 'test2', 'test3']);
    }

    public function invalidValuesProvider(): \Iterator
    {
        yield [[123]];
        yield [['test', 123]];
        yield [['test', 123.123, false]];
        yield [[123, 'test', true]];
        yield [[true, 'test', true]];
        yield [['test', 123, true, 'other']];
    }

    /**
     * @dataProvider invalidValuesProvider
     */
    public function testWithInvalidArray(array $value): void
    {
        $stringPrototype = new StringPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $stringPrototype->validatePrototype($value);
    }

    /**
     * @dataProvider invalidValuesProvider
     */
    public function testInvalidWithArrayNode(array $value): void
    {
        $arrayNode = new ArrayNode('array_node',new StringPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $arrayNode->validateType($value);
    }
}
