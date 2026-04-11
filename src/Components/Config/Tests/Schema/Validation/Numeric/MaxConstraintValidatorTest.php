<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Validation\Numeric;

use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use PHPUnit\Framework\TestCase;
use Kaizen\Components\Config\Schema\Validation\Numeric\MaxConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(MaxConstraintValidator::class)]
class MaxConstraintValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new MaxConstraintValidator(10);

        self::assertTrue($validator->validate(10, new IntegerNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate(12, new IntegerNode('node'), new NodeListSchema()));

        self::assertEquals('The value must be less than or equal to 10', $validator->getMessage());
    }
}