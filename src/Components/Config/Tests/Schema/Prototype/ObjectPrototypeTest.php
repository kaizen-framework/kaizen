<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversClass(ObjectPrototype::class)]
class ObjectPrototypeTest extends AbstractPrototypeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[
            [
                'node1' => 'string',
                'node2' => 233,
                'node3' => true,
                'node4' => ['ok', 123, true],
                'node5' => ['key1' => 'string'],
            ],
            [
                'node6' => 'string',
                'node7' => 233,
                'node8' => true,
            ],
        ]];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield 'With a entry without key' => [[
            [
                'string',
                'node2' => 233,
                'node3' => true,
                'node4' => ['ok', 123, true],
                'node5' => ['key1' => 'string'],
            ],
            [
                'node6' => 'string',
                'node7' => 233,
                'node8' => true,
            ],
        ], 'test[0]', 'All children should have a key.'];
        yield 'With flat object' => [[
            'node2' => 233,
            'node3' => true,
            'node4' => ['ok', 123, true],
            'node5' => ['key1' => 'string'],
        ], 'test.node2', 'Expected an object, got int.'];
    }

    protected function getPrototype(): AbstractPrototype
    {
        return new ObjectPrototype();
    }

    public static function valueWithValidationResultProvider(): \Iterator
    {
        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            [],
        ], 'test[1].node1', 'The value is required but was not provided or is null.'];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => 123, 'node2' => true, 'node3' => true, 'node4' => 123],
        ], 'test[1].node2', 'Expected a string, but got bool.'];

        yield [[
            'string',
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ], 'test[0]', 'Expected an object, got string.'];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node32' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ], 'test[0]', 'Unknown "node32", allowed keys are: ["node1", "node2", "node3", "node4"]. Did you mean "node3" ?'];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => 123, 'node4' => 'string'],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 123, 'node3' => 'string', 'node45' => 123],
        ], 'test[0].node3', 'Expected a boolean, but got int.'];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ], null, null];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true],
            ['node1' => true, 'node3' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ], null, null];
    }

    /**
     * @param array<int, array<string, mixed>> $value
     */
    #[DataProvider('valueWithValidationResultProvider')]
    public function testValidationWithSchema(array $value, ?string $expectedPath, ?string $expectedMessage): void
    {
        $objectPrototype = new ObjectPrototype(
            new ScalarNode('node1')->required(),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')
        );

        $validationResult = $objectPrototype->isValueSatisfiesPrototypeSpecification(
            $value,
            new ValidationContext('test')
        );

        if (null === $expectedPath) {
            self::assertTrue($validationResult->isValid());

            return;
        }

        self::assertSame(
            $expectedPath,
            $validationResult->errors[0]->path
        );
        self::assertSame(
            $expectedMessage,
            $validationResult->errors[0]->message
        );
    }

    public function testNormalization(): void
    {
        $objectPrototype = new ObjectPrototype();

        self::assertEquals(['test' => 'unit'], $objectPrototype->normalize(['test' => 'unit']));
    }

    public static function valueWithNormalizedResultProvider(): \Iterator
    {
        yield [[
            ['node1' => false, 'node2' => 'string', 'node3' => true, 'node4' => 321],
            [],
        ], [
            ['node1' => false, 'node2' => 'string', 'node3' => true, 'node4' => 321],
            ['node1' => true, 'node4' => 123],
        ]];

        yield [[
            ['node1' => false, 'node2' => 'string'],
            ['node1' => true, 'node4' => 111],
        ], [
            ['node1' => false, 'node2' => 'string', 'node4' => 123],
            ['node1' => true, 'node4' => 111],
        ]];

        yield [[
            ['node1' => false, 'node2' => 'string', 'node3' => true, 'node4' => 321],
            ['node1' => false, 'node2' => null, 'node4' => 333],
            null
        ], [
            ['node1' => false, 'node2' => 'string', 'node3' => true, 'node4' => 321],
            ['node1' => false, 'node2' => null, 'node4' => 333],
        ]];
    }

    /**
     * @param array<int, array<string, mixed>> $value
     * @param array<int, array<string, mixed>> $expectedResult
     */
    #[DataProvider('valueWithNormalizedResultProvider')]
    public function testNormalizationWithSchema(array $value, array $expectedResult): void
    {
        $objectPrototype = new ObjectPrototype(
            new BooleanNode('node1')->defaultValue(true),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')->defaultValue(123),
        );

        self::assertEquals($expectedResult, $objectPrototype->normalize($value));
    }
}
