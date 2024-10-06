<?php

namespace App\Components\Config\Tests\Schema\Node;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ScalarNode;
use PHPUnit\Framework\TestCase;

class ScalarNodeTest extends TestCase
{
    public function validValueProvider(): \Iterator
    {
        yield ['string'];
        yield [123];
        yield [true];
        yield [123.123];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testValidateType(): void
    {
        $node = new ScalarNode('scalar');
        $node->validateType(true);

        self::assertEquals('scalar', $node->getKey());
    }

    public function invalidValueProvider(): \Iterator
    {
        yield [[]];
        yield [new \stdClass()];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testException(mixed $value): void
    {
        $node = new ScalarNode('scalar');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
