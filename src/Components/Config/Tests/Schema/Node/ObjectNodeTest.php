<?php

declare(strict_types=1);

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\ConfigSchema;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Node\IntegerNode;
use App\Components\Config\Schema\Node\StringNode;
use App\Components\Config\Schema\Node\ObjectNode;
use App\Components\Config\Schema\Prototype\ObjectPrototype;
use App\Components\Config\Schema\Prototype\TuplePrototype;
use App\Components\Config\Schema\Prototype\TupleTypesEnum;
use PHPUnit\Framework\TestCase;

class ObjectNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new ObjectNode(
            'object',
            new ConfigSchema(
                new ArrayNode('tuple_array', new TuplePrototype(
                    TupleTypesEnum::SCALAR,
                    TupleTypesEnum::INTEGER,
                    TupleTypesEnum::BOOLEAN
                )),
                new ArrayNode('object_array', new ObjectPrototype(
                    new StringNode('sub_object_array')
                )),
                new StringNode('string'),
                new IntegerNode('int'),
            )
        );

        $node->validateType([
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]);

        self::assertEquals('object', $node->getKey());
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [[
            'tuple_array' => ['string', true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array_invalid_name' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => true,
            'int' => 123,
        ]];

        yield [[
            'tuple_array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 'other string',
        ]];

        yield [[
            'invalid_key' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testException(array $value): void
    {
        $node = new ObjectNode('object', new ConfigSchema(
            new ArrayNode('tuple_array', new TuplePrototype(
                TupleTypesEnum::SCALAR,
                TupleTypesEnum::INTEGER,
                TupleTypesEnum::BOOLEAN
            )),
            new ArrayNode('object_array', new ObjectPrototype(
                new StringNode('sub_object_array')
            )),
            new StringNode('string'),
            new IntegerNode('int'),
        ));

        self::expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}