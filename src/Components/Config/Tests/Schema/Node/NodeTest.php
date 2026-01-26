<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema\Node;

use Kaizen\Components\Config\Processor\ValidationContext;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Validation\String\LengthConstraintValidator;
use PHPUnit\Framework\TestCase;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Node\Node;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(Node::class)]
class NodeTest extends TestCase
{
    public function testConstraint(): void
    {
        $node = new StringNode('node');

        $node->addConstraintValidator(new LengthConstraintValidator(5, 10));
        $node->setConstraintValidators([new LengthConstraintValidator(5, 10)]);

        self::assertCount(1, $node->constraintValidators);

        $validationResult = $node->validateConstraints('12345', new NodeListSchema(), new ValidationContext());

        self::assertTrue($validationResult->isValid());

        $validationResult = $node->validateConstraints('1234', new NodeListSchema(), new ValidationContext());

        self::assertFalse($validationResult->isValid());
    }
}