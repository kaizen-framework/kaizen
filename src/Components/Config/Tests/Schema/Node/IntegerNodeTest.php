<?php

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\ConfigProcessingException;
use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\FloatNode;
use App\Components\Config\Schema\Node\IntegerNode;
use PHPUnit\Framework\TestCase;

class IntegerNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new IntegerNode('integer');
        $node->validateType(123);

        self::assertEquals('integer', $node->getKey());
    }

    public function testProcessValue(): void
    {
        $node = new IntegerNode('int');
        $node->min(10);

        self::expectException(ConfigProcessingException::class);
        $node->processValue(1);
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [true];
        yield ['test'];
        yield [123.123];
        yield [[]];
        yield [new \stdClass()];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testException(mixed $value): void
    {
        $node = new IntegerNode('integer');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
