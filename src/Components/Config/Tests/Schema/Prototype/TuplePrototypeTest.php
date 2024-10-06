<?php

namespace App\Components\Config\Tests\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Prototype\TuplePrototype;
use App\Components\Config\Schema\Prototype\TupleTypesEnum;
use PHPUnit\Framework\TestCase;

class TuplePrototypeTest extends TestCase
{
    public function testValidateArray(): void
    {
        self::expectNotToPerformAssertions();

        $tuplePrototype = new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN,
            TupleTypesEnum::FLOAT,
            TupleTypesEnum::SCALAR
        );

        $tuplePrototype->validatePrototype(['string', 123, true, 12.12, 'scalar']);
    }

    public function testValidateWithArrayNode(): void
    {
        self::expectNotToPerformAssertions();

        $tupleNode = new ArrayNode('tuple', new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        ));

        $tupleNode->validateType(['string', 123, true]);
    }

    public function invalidTupleValueProvider(): \Iterator
    {
        yield [[123]];
        yield [['test', 123]];
        yield [['test', 123.123, false]];
        yield [[123, 'test', true]];
        yield [[true, 'test', true]];
        yield [[12, 23.32, true]];
        yield [['test', 123, true, 'other']];
    }

    /**
     * @dataProvider invalidTupleValueProvider
     */
    public function testTupleException(mixed $value): void
    {
        $tuplePrototype = new TuplePrototype(
            TupleTypesEnum::FLOAT,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        );

        self::expectException(InvalidNodeTypeException::class);
        $tuplePrototype->validatePrototype($value);
    }

    /**
     * @dataProvider invalidTupleValueProvider
     */
    public function testTupleExceptionWithArrayNode(mixed $value): void
    {
        $tupleNode = new ArrayNode('tuple', new TuplePrototype(
            TupleTypesEnum::STRING,
            TupleTypesEnum::INTEGER,
            TupleTypesEnum::BOOLEAN
        ));

        self::expectException(InvalidNodeTypeException::class);
        $tupleNode->validateType($value);
    }
}
