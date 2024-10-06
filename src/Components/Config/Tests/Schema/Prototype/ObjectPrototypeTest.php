<?php

namespace App\Components\Config\Tests\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Node\BooleanNode;
use App\Components\Config\Schema\Node\IntegerNode;
use App\Components\Config\Schema\Node\ScalarNode;
use App\Components\Config\Schema\Node\StringNode;
use App\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\TestCase;

class ObjectPrototypeTest extends TestCase
{
    public function testValidate(): void
    {
        self::expectNotToPerformAssertions();

        $objectPrototype = new ObjectPrototype();
        $objectPrototype->validatePrototype([
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
        ]);
    }

    public function testValidateWithDefinedEntries()
    {
        $this->expectNotToPerformAssertions();

        $objectPrototype = new ObjectPrototype(
            new ScalarNode('node1'),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')
        );
        $objectPrototype->validatePrototype([
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => 123, 'node2' => 'string2', 'node3' => true, 'node4' => 321],
            ['node1' => 'string', 'node2' => 'string3', 'node3' => true, 'node4' => 231],
        ]);
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            []
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => 123, 'node2' => true, 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            [],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123]
        ]];

        yield [[
            'string',
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node32' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123]
        ]];

        yield [[
            ['node1' => true, 'node2' => 'string', 'node3' => 123, 'node4' => 'string'],
            ['node1' => true, 'node2' => 'string', 'node3' => true, 'node4' => 123],
            ['node1' => true, 'node2' => 123, 'node3' => 'string', 'node45' => 123]
        ]];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testWithInvalidValue(array $value): void
    {
        $objectPrototype = new ObjectPrototype(
            new ScalarNode('node1'),
            new StringNode('node2'),
            new BooleanNode('node3'),
            new IntegerNode('node4')
        );

        self::expectException(InvalidNodeTypeException::class);
        $objectPrototype->validatePrototype($value);
    }
}
