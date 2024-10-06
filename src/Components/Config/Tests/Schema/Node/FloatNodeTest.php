<?php

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\ConfigProcessingException;
use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\FloatNode;
use PHPUnit\Framework\TestCase;

class FloatNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new FloatNode('float');
        $node->validateType(12.3);

        self::assertEquals('float', $node->getKey());
    }

    public function testProcessValue(): void
    {
        $node = new FloatNode('float');
        $node->min(10.5);

        self::expectException(ConfigProcessingException::class);
        $node->processValue(1.3);
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [true];
        yield ['test'];
        yield [123];
        yield [[]];
        yield [new \stdClass()];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testException(mixed $value): void
    {
        $node = new FloatNode('float');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
