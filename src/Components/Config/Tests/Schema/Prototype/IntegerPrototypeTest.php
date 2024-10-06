<?php

namespace App\Components\Config\Tests\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Prototype\IntegerPrototype;
use PHPUnit\Framework\TestCase;

class IntegerPrototypeTest extends TestCase
{
    public function testValidateArray()
    {
        self::expectNotToPerformAssertions();

        $scalarPrototype = new IntegerPrototype();
        $scalarPrototype->validatePrototype([123, 12]);
    }

    public function testValidateWithArrayNode()
    {
        self::expectNotToPerformAssertions();

        $scalarPrototype = new ArrayNode('node', new IntegerPrototype());
        $scalarPrototype->validateType([123, 12]);
    }

    public function invalidIntegerArrayValueProvider(): \Iterator
    {
        yield [[12.12]];
        yield [['string']];
        yield [[true]];
        yield [[true, 'test']];
    }

    /**
     * @dataProvider invalidIntegerArrayValueProvider
     */
    public function testIntegerException(mixed $value): void
    {
        $scalarPrototype = new IntegerPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $scalarPrototype->validatePrototype($value);
    }

    /**
     * @dataProvider invalidIntegerArrayValueProvider
     */
    public function testIntegerExceptionWithArrayNode(mixed $value): void
    {
        $scalarArrayNode = new ArrayNode('node', new IntegerPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $scalarArrayNode->validateType($value);
    }
}
