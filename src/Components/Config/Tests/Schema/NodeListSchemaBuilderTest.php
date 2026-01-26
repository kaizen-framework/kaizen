<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Schema;

use Kaizen\Components\Config\Schema\Node\Node;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\FloatNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\ScalarNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(NodeListSchemaBuilder::class)]
class NodeListSchemaBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $configSchemaBuilder = new NodeListSchemaBuilder();

        $configSchema = $configSchemaBuilder
            ->integer('int')
            ->min(10)
            ->max(100)
            ->required()
            ->buildNode();
        $configSchema
            ->float('float')
            ->min(0.1)
            ->max(10.20)
            ->defaultValue(0.1)
            ->validateClosure(static fn (float $value) => $value % 2 === 0, '')
            ->buildNode();
        $configSchema
            ->string('string')
            ->required()
            ->defaultValue('default_string')
            ->buildNode();
        $configSchema
            ->child('object')
                ->scalar('scalar')
                    ->defaultValue('default_scalar')
                ->buildNode()
                ->boolean('boolean')->buildNode()
            ->buildChildNode();
        $configSchema
            ->objectVariable('objectVariable')
            ->withPrototype()->object(static fn (NodeListSchemaBuilder $builder) => $builder
                ->string('string')->buildNode()
                ->boolean('boolean')->buildNode()
                ->build()
            )
            ->buildNode();
        $configSchema
            ->array('array')
            ->withPrototype()->scalar()
            ->buildNode();

        $configSchema = $configSchema->build();

        $this->assertInstanceOf(NodeListSchema::class, $configSchema);

        $integerNode = $configSchema->getNode('int');
        $this->assertInstanceOf(IntegerNode::class, $integerNode);
        $this->assertCount(2, $integerNode->constraintValidators);

        $floatNode = $configSchema->getNode('float');
        $this->assertInstanceOf(FloatNode::class, $floatNode);
        $this->assertCount(3, $floatNode->constraintValidators);

        $stringNode = $configSchema->getNode('string');
        $this->assertInstanceOf(StringNode::class, $stringNode);
        $this->assertTrue($stringNode->isRequired);
        $this->assertSame('default_string', $stringNode->defaultValue);

        $objectNode = $configSchema->getNode('object');
        $this->assertInstanceOf(ObjectNode::class, $objectNode);
        $childClasses = array_map(
            static fn (Node $node): string => $node::class,
            $objectNode->nodeListSchema->nodes
        );
        $this->assertSame([ScalarNode::class, BooleanNode::class], $childClasses);
    }
}
