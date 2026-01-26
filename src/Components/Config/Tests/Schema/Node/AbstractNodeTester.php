<?php

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Schema\Node\Node;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractNodeTester extends TestCase
{
    abstract public static function validValueProvider(): \Iterator;

    #[DataProvider('validValueProvider')]
    public function testTypeValidationWithValidValue(mixed $value): void
    {
        $node = $this->getNode();

        $validationResult = $node->isValueSatisfiesNodeSpecification($value, new ValidationContext());

        self::assertTrue($validationResult->isValid());
    }

    abstract public static function invalidValueProvider(): \Iterator;

    #[DataProvider('invalidValueProvider')]
    public function testTypeValidationWithInvalidValue(mixed $value, string $expectedPath, string $expectedMessage): void
    {
        $node = $this->getNode();

        $validationResult = $node->isValueSatisfiesNodeSpecification($value, new ValidationContext());

        self::assertFalse($validationResult->isValid());
        self::assertSame(
            $expectedPath,
            $validationResult->errors[0]->path
        );
        self::assertSame(
            $expectedMessage,
            $validationResult->errors[0]->message
        );
    }

    public function testCanNotHaveNullValueWhenNodeIsRequired(): void
    {
        $node = $this->getNode()->required();

        $validationResult = $node->isValueSatisfiesNodeSpecification(null, new ValidationContext());

        self::assertFalse($validationResult->isValid());
        self::assertEquals(
            $node->key,
            $validationResult->errors[0]->path
        );
        self::assertEquals(
            'The value is required but was not provided or is null.',
            $validationResult->errors[0]->message
        );
    }

    /**
     * @return Node<mixed>
     */
    abstract protected function getNode(): Node;
}