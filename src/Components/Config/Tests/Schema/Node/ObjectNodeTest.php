<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Validation\NodeConstraintValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversClass(ObjectNode::class)]
class ObjectNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ]];
        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['another', 1, false],
            'object_array' => [
                ['sub_object_array' => 'another string'],
            ],
            'string' => 'again another string',
            'int' => 1,
        ]];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => true,
            'int' => 123,
        ], 'object.string', 'Expected a string, but got bool.'];

        yield [[
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ], 'object.required', 'The value is required but was not provided or is null.'];

        yield [[
            'required' => [],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ], 'object.required.scalar', 'The value is required but was not provided or is null.'];

        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => true,
            'int' => 123,
        ], 'object.string', 'Expected a string, but got bool.'];

        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 'other string',
        ], 'object.int', 'Expected an int, but got string.'];

        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'invalid_key' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ], 'object', 'Unknown "invalid_key", allowed keys are: ["required", "array", "object_array", "string", "int"].'];

        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'aray' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string' => 'string',
            'int' => 123,
        ], 'object', 'Unknown "aray", allowed keys are: ["required", "array", "object_array", "string", "int"]. Did you mean "array" ?'];

        yield [[
            'required' => [
                'scalar' => 'value'
            ],
            'array' => ['string', 123, true],
            'object_array' => [
                ['sub_object_array' => 'string'],
            ],
            'string',
            'int' => 123,
        ], 'object', 'All children should have a key.'];

        yield [true, 'object', 'Expected a node with children, but got bool.'];
        yield ['string', 'object', 'Expected a node with children, but got string.'];
    }

    public static function nodeValueProvider(): \Iterator
    {
        yield 'null value and default value empty array' => [null, [], ['integer' => 123]];
        yield 'null value and default value' => [null, ['integer' => 321, 'string' => 'string'], ['integer' => 321, 'string' => 'string']];
        yield 'null value and default value with wrong keys' => [null, ['key' => 'value', 'string' => 'string'], ['key' => 'value', 'integer' => 123, 'string' => 'string']];
        yield 'with value and default value' => [[
            'scalar' => true,
            'integer' => 321,
            'string' => 'string',
            'child' => [
            ]
        ], ['key' => 'value'], [
            'scalar' => true,
            'integer' => 321,
            'string' => 'string',
            'child' => [
                'float' => 12.12,
            ]
        ]];
        yield 'with some null value' => [[
            'scalar' => true,
            'integer' => 321,
            'string' => null,
            'child' => [
            ]
        ], ['key' => 'value'], [
            'scalar' => true,
            'integer' => 321,
            'string' => null,
            'child' => [
                'float' => 12.12,
            ]
        ]];
    }

    /**
     * @param array<string, mixed>|null $actualValue
     * @param array<string, mixed>|null $defaultValue
     * @param array<string, mixed>|null $expectedResult
     */
    #[DataProvider('nodeValueProvider')]
    public function testNormalizeNode(?array $actualValue, ?array $defaultValue, ?array $expectedResult): void
    {
        $node = new ObjectNode('object', new NodeListSchema(
            new ScalarNode('scalar'),
            new IntegerNode('integer')->defaultValue(123),
            new StringNode('string'),
            new ObjectNode('child', new NodeListSchema(
                new ScalarNode('scalar')->required(),
                new FloatNode('float')->defaultValue(12.12)
            )),
        ));

        if (null !== $defaultValue) {
            $node->defaultValue($defaultValue);
        }

        self::assertEquals(
            $expectedResult,
            $node->normalize($actualValue)
        );
    }

    public function testValidateConstraints(): void
    {
        $value = [
            'scalar' => 'value',
            'integer' => 123,
            'string' => 'string',
            'child' => [
                'scalar' => 'value',
                'float' => 12.12,
            ],
        ];

        $constraint = $this->createMock(NodeConstraintValidatorInterface::class);

        $floatNode = new FloatNode('float')->addConstraintValidator($constraint)->defaultValue(12.12);

        $validationContext = new ValidationContext();
        $nodeListSchema = new NodeListSchema();

        $constraint->expects($this->once())
            ->method('validate')
            ->with(12.12, $floatNode, $nodeListSchema)
            ->willReturn(true);

        $objectNode = new ObjectNode('object', new NodeListSchema(
            new ScalarNode('scalar'),
            new IntegerNode('integer')->defaultValue(123),
            new StringNode('string'),
            new ObjectNode('child', new NodeListSchema(
                new ScalarNode('scalar')->required(),
                new FloatNode('float')->addConstraintValidator($constraint)->defaultValue(12.12)
            )),
        ));

        $objectNode->validateConstraints($value, $nodeListSchema, $validationContext);
    }

    protected function getNode(): Node
    {
        return new ObjectNode('object', new NodeListSchema(
            new ObjectNode('required', new NodeListSchema(
                new ScalarNode('scalar')->required(),
                new IntegerNode('integer')->defaultValue(123),
            ))->required(),
            new ArrayNode('array'),
            new ArrayNode('object_array'),
            new StringNode('string'),
            new IntegerNode('int'),
        ));
    }
}
