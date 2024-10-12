<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ScalarNodeTest extends TestCase
{
    public static function validValueProvider(): \Iterator
    {
        yield ['string'];
        yield [123];
        yield [true];
        yield [123.123];
    }

    #[DataProvider('validValueProvider')]
    public function testValidateType(): void
    {
        $node = new ScalarNode('scalar');
        $node->validateType(true);

        self::assertEquals('scalar', $node->getKey());
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [[]];
        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $node = new ScalarNode('scalar');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
