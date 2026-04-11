<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Validation\Numeric;

use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use PHPUnit\Framework\TestCase;
use Kaizen\Components\Config\Schema\Validation\Numeric\MinConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(MinConstraintValidator::class)]
class MinConstraintValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new MinConstraintValidator(10);

        self::assertTrue($validator->validate(10, new IntegerNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate(9, new IntegerNode('node'), new NodeListSchema()));

        self::assertEquals('The value must be greater than or equal to 10', $validator->getMessage());
    }
}