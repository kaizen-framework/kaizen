<?php

namespace App\Components\Config\Tests\Schema\Prototype;

use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Prototype\ScalarPrototype;
use PHPUnit\Framework\TestCase;

class ScalarPrototypeTest extends TestCase
{
    public function testValidateArray()
    {
        self::expectNotToPerformAssertions();

        $scalarPrototype = new ScalarPrototype();
        $scalarPrototype->validatePrototype([123, 12.3, true, 'string']);
    }

    public function testValidateWithArrayNode()
    {
        self::expectNotToPerformAssertions();

        $scalarPrototype = new ArrayNode('node', new ScalarPrototype());
        $scalarPrototype->validateType([123, 12.3, true, 'string']);
    }

    public function invalidScalarArrayValueProvider(): \Iterator
    {
        yield [[[]]];
        yield [[new \stdClass()]];
        yield [[null]];
    }

    /**
     * @dataProvider invalidScalarArrayValueProvider
     */
    public function testScalarException(array $value): void
    {
        $scalarPrototype = new ScalarPrototype();

        self::expectException(InvalidNodeTypeException::class);
        $scalarPrototype->validatePrototype($value);
    }

    /**
     * @dataProvider invalidScalarArrayValueProvider
     */
    public function testScalarExceptionWithArrayNode(array $value): void
    {
        $scalarArrayNode = new ArrayNode('node', new ScalarPrototype());

        self::expectException(InvalidNodeTypeException::class);
        $scalarArrayNode->validateType($value);
    }
}
