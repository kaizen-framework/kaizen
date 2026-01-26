<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Validation\String;

use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use PHPUnit\Framework\TestCase;
use Kaizen\Components\Config\Schema\Validation\String\LengthConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(LengthConstraintValidator::class)]
class LengthConstraintValidatorTest extends TestCase
{
    public function testValidateMinLength(): void
    {
        $validator = new LengthConstraintValidator(5, null);

        self::assertTrue($validator->validate('success', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));

        self::assertEquals('The value length must be greater than or equals to 5 characters.', $validator->getMessage());
    }

    public function testValidateMaxLength(): void
    {
        $validator = new LengthConstraintValidator(null, 3);

        self::assertTrue($validator->validate('foo', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));

        self::assertEquals('The value length must be less than or equals to 3 characters.', $validator->getMessage());
    }

    public function testValidateWithRange(): void
    {
        $validator = new LengthConstraintValidator(5, 10);

        self::assertTrue($validator->validate('success', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate(
            'fail because exceed the max length',
            new StringNode('node'),
            new NodeListSchema()
        ));

        self::assertEquals('The value length must be between 5 and 10 characters.', $validator->getMessage());
    }

    public function testCanNotProvideTwoNullValue(): void
    {
        $this->expectException(\LogicException::class);

        $validator = new LengthConstraintValidator(null, null);
    }
}