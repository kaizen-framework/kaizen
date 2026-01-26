<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Processor\ValueObject\ValidationResult;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ArrayNode::class)]
class ArrayNodeTest extends AbstractNodeTester
{
    public static function validValueProvider(): \Iterator
    {
        yield [[true]];
        yield [['string', 123, true]];
        yield [[1, 2, 3]];
        yield [[null, 1, 1.1, true, 'string']];
        yield [null];
    }

    public static function invalidValueProvider(): \Iterator
    {
        yield [123, 'node', 'Expected an array, but got int.'];
        yield [12.3, 'node' , 'Expected an array, but got float.'];
        yield ['string', 'node', 'Expected an array, but got string.'];
        yield [true, 'node', 'Expected an array, but got bool.'];
    }

    public function testNormalize(): void
    {
        $node = new ArrayNode('array');

        $result = $node->normalize(['test']);

        self::assertSame(['test'], $result);
    }

    public function testNormalizeWithPrototype(): void
    {
        $prototype = $this->createMock(AbstractPrototype::class);
        $prototype->expects(self::once())
            ->method('normalize')
            ->with(['test'])
            ->willReturn(['test']);

        $node = new ArrayNode('array', $prototype);

        $node->normalize(['test']);
    }

    public function testPrototypeValidationFail(): void
    {
        $value = [
            'parameter' => 'value',
        ];

        $validationContext = new ValidationContext();

        $prototypeValidationResult = new ValidationResult();
        $prototypeValidationResult->addError('test', 'message');

        $prototypeMock = $this->createMock(AbstractPrototype::class);
        $prototypeMock->expects(self::once())
            ->method('isValueSatisfiesPrototypeSpecification')
            ->with($value, $validationContext->child('array'))
            ->willReturn($prototypeValidationResult);
        ;

        $arrayNode = new ArrayNode('array', $prototypeMock);

        $validationResult = $arrayNode->isValueSatisfiesNodeSpecification($value, $validationContext);

        self::assertFalse($validationResult->isValid());
        self::assertSame(
            'test',
            $validationResult->errors[0]->path
        );
        self::assertSame(
            'message',
            $validationResult->errors[0]->message
        );
    }

    protected function getNode(): Node
    {
        return new ArrayNode('node');
    }
}
