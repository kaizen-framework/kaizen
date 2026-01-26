<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Validation\String;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\String\PatternConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PatternConstraintValidator::class)]
class PatternConstraintValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $validator = new PatternConstraintValidator('/^[a-zA-Z_-]+:\/\/.*?:.*?@[a-zA-Z_-]+:[0-9]{2,5}\/\w+/');

        self::assertTrue($validator->validate(
            'pgsql://user:password@localhost:5432/database',
            new StringNode('node'),
            new NodeListSchema()
        ));
        self::assertFalse($validator->validate(
            'wrong pattern',
            new StringNode('node'),
            new NodeListSchema()
        ));
        self::assertFalse($validator->validate(
            'pgsql://user@localhost:5432/database',
            new StringNode('node'),
            new NodeListSchema()
        ));

        self::assertEquals(
            'The value not match the designed pattern "/^[a-zA-Z_-]+:\/\/.*?:.*?@[a-zA-Z_-]+:[0-9]{2,5}\/\w+/"',
            $validator->getMessage()
        );
    }

    public function testGetMessage(): void
    {
        $validator = new PatternConstraintValidator('/^\w+/', 'message');

        self::assertEquals(
            'message',
            $validator->getMessage()
        );
    }

    public function testWithInvalidPattern(): void
    {
        $validator = new PatternConstraintValidator('invalid');

        $this->expectException(\RuntimeException::class);

        @$validator->validate(
            'pgsql://user:password@localhost:5432/database',
            new StringNode('node'),
            new NodeListSchema()
        );
    }
}