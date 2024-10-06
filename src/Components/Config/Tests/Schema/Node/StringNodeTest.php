<?php

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\StringNode;
use PHPUnit\Framework\TestCase;

class StringNodeTest extends TestCase
{
    public function testValidateType()
    {
        $node = new StringNode('string');
        $node->validateType('okok');

        self::assertEquals('string', $node->getKey());
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [true];
        yield [123];
        yield [123.22];
        yield [[]];
        yield [new \stdClass()];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testException(mixed $value): void
    {
        $node = new StringNode('string');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
