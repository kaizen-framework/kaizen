<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Exception\InvalidNodeTypeException;
use Kaizen\Components\Config\Schema\Node\NumericNode;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
 #[CoversClass(NumericNode::class)]
class NumericNodeTest extends TestCase
{
    public function testValidateType(): void
    {
        $node = new NumericNode('numeric');

        $node->validateType(12.3);
        $node->validateType(12);

        self::assertEquals('numeric', $node->getKey());
    }

    public static function invalidNumericValueProvider(): \Iterator
    {
        yield 'With int value under the min' => [
            2, 10, 1
        ];

        yield 'With float value under the min' => [
            2.5, 10.5, 1.5
        ];

        yield 'With int value over the min' => [
            10, 105, 150
        ];

        yield 'With float value over the min' => [
            2.5, 10.5, 19.5
        ];
    }

    #[DataProvider('invalidNumericValueProvider')]
    public function testProcessValue(int|float $min, int|float $max, int|float $actualValue): void
    {
        $node = new NumericNode('numeric');
        $node->min($min)->max($max);

        self::expectException(ConfigProcessingException::class);

        $node->processValue($actualValue);
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [true];
        yield ['test'];
        yield [[]];
        yield [new \stdClass()];
    }

    #[DataProvider('invalidValueProvider')]
    public function testException(mixed $value): void
    {
        $node = new NumericNode('numeric');

        $this->expectException(InvalidNodeTypeException::class);
        $node->validateType($value);
    }
}
