<?php

namespace Kaizen\Components\Config\Tests\Schema\Validation;

use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\CompositeValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(CompositeValidator::class)]
class CompositeValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        /** @var CompositeValidator<string> $validator */
        $validator = new CompositeValidator(
            static fn (string $value) => $value === 'fail',
            false,
            'message'
        );

        self::assertTrue($validator->validate('valid', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));
        self::assertEquals('message', $validator->getMessage());
    }

    public function testValidateCallback(): void
    {
        /** @var CompositeValidator<string> $validator */
        $validator = new CompositeValidator(
            static fn (string $value) => strlen($value) >= 4,
            static fn (string $value) => $value !== 'fail',
            'message'
        );

        self::assertTrue($validator->validate('valid', new StringNode('node'), new NodeListSchema()));
        self::assertFalse($validator->validate('fail', new StringNode('node'), new NodeListSchema()));
    }
}
