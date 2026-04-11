<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Validation;

use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\ClosureConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ClosureConstraintValidator::class)]
class ClosureConstraintValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new ClosureConstraintValidator(
            static fn (string $value) => strlen($value) > 4,
            'message'
        );

        self::assertTrue($validator->validate('success', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));
        self::assertEquals('message', $validator->getMessage());
    }
}