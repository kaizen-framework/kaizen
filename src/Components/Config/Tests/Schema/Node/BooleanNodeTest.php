<?php

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\BooleanNode;
use PHPUnit\Framework\TestCase;

class BooleanNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new BooleanNode('boolean');
        $node->validateType(true);

        self::assertEquals('boolean', $node->getKey());
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [123];
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
        $node = new BooleanNode('boolean');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
