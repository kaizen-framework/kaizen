<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Node\ObjectVariableNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ObjectVariableNode::class)]
class ObjectVariableNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[
            'variable' => 'test',
            'int' => 123,
            'bool' => true,
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ]];
        yield [[
            'foo' => 'value',
            'bar' => 11,
            'baz' => 1.1,
            'bool' => true,
            'array' => ['a', 1],
            'object' => ['key' => 'value'],
        ]];
        yield [[]];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true, 'node', 'Expected a list of values, but got bool.'];
        yield [1, 'node', 'Expected a list of values, but got int.'];
        yield [1.1, 'node', 'Expected a list of values, but got float.'];
        yield [[
            'variable' => 'test',
            'no keys',
            'bool' => true,
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ], 'node', 'All children should have a key.'];
        yield [['a', 1], 'node', 'All children should have a key.'];
    }

    protected function getNode(): Node
    {
        return new ObjectVariableNode('node');
    }

    public function testPrototypeValidationIsCalled(): void
    {
        $value = [
            'variable' => 'test',
            'parameters' => [
                'parameter1' => 'test',
                'parameter2' => 'test2',
            ],
        ];

        $validationContext = new ValidationContext();

        $prototypeValidationResult = new ValidationResult();
        $prototypeValidationResult->addError('path', 'message');

        $prototypeMock = $this->createMock(AbstractPrototype::class);
        $prototypeMock->expects(self::once())
            ->method('isValueSatisfiesPrototypeSpecification')
            ->with($value, $validationContext->child('node'))
            ->willReturn($prototypeValidationResult);
        ;

        $objectVariableNode = new ObjectVariableNode('node', $prototypeMock);

        $validationResult = $objectVariableNode->isValueSatisfiesNodeSpecification($value, $validationContext);

        self::assertSame(
            'path',
            $validationResult->errors[0]->path
        );
        self::assertSame(
            'message',
            $validationResult->errors[0]->message
        );
    }

    public function testNormalization(): void
    {
        $node = new ObjectVariableNode('node');

        $config = [
            'variable' => 'test',
            'parameters' => [],
            'boolean' => true,
        ];

        self::assertEquals($config, $node->normalize($config));
    }

    public function testNormalizationWithPrototype(): void
    {
        /** @var array<string, array<string, mixed>> $config */
        $config = [
            'param1' => [
                'parameters' => [],
                'boolean' => true,
            ],
            'param2' => [
                'variable' => 'test',
                'parameters' => [],
            ],
        ];

        $prototype = $this->createMock(AbstractPrototype::class);


        $prototype->expects(self::once())
            ->method('normalize')
            ->with($config)
            ->willReturn([
                'param1' => [
                    'parameters' => [],
                    'boolean' => true,
                    'default' => 'value',
                ],
                'param2' => [
                    'variable' => 'test',
                    'parameters' => [],
                    'default' => 'value',
                ]
            ]);

        $node = new ObjectVariableNode('node', $prototype);

        self::assertEquals([
            'param1' => [
                'parameters' => [],
                'boolean' => true,
                'default' => 'value',
            ],
            'param2' => [
                'variable' => 'test',
                'parameters' => [],
                'default' => 'value',
            ],
        ], $node->normalize($config));
    }
}
