<?php

namespace Kaizen\Components\Config\Tests\Schema;

use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Node\ArrayNode;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NodeListSchema::class)]
class NodeListSchemaTest extends TestCase
{
    public function testGetKeyWithLevenshtein(): void
    {
        $nodeListSchema = new NodeListSchema(
            new BooleanNode('is_test'),
            new StringNode('test_string'),
            new ObjectNode('parameters', new NodeListSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new NodeListSchema(
                    new StringNode('parameter4'),
                    new ArrayNode('parameter5', new ObjectPrototype(
                        new StringNode('parameter6'),
                        new IntegerNode('parameter7')
                    )),
                )),
            )),
        );

        self::assertNotNull($nodeListSchema->getNode('is_test'));
    }
}
