<?php

namespace Kaizen\Components\Config\Tests\Schema\Prototype;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Schema\Prototype\AbstractPrototype;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class AbstractPrototypeTester extends TestCase
{
    abstract public static function validValueProvider(): \Iterator;

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('validValueProvider')]
    public function testTypeValidationWithValidValue(array $value): void
    {
        $prototype = $this->getPrototype();

        $validationResult = $prototype->isValueSatisfiesPrototypeSpecification($value, new ValidationContext('test'));

        self::assertTrue($validationResult->isValid());
    }

    abstract public static function invalidValueProvider(): \Iterator;

    /**
     * @param array<int, mixed> $value
     */
    #[DataProvider('invalidValueProvider')]
    public function testTypeValidationWithInvalidValue(array $value, string $expectedPath, string $expectedMessage): void
    {
        $prototype = $this->getPrototype();

        $validationResult = $prototype->isValueSatisfiesPrototypeSpecification($value, new ValidationContext('test'));

        self::assertFalse($validationResult->isValid());
        self::assertSame(
            $expectedMessage,
            $validationResult->errors[0]->message
        );
        self::assertSame(
            $expectedPath,
            $validationResult->errors[0]->path
        );
    }

    abstract protected function getPrototype(): AbstractPrototype;
}