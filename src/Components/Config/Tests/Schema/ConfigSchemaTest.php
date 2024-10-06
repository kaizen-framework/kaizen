<?php

namespace App\Components\Config\Tests\Schema;

use App\Components\Config\Exception\InvalidSchemaException;
use App\Components\Config\Schema\ConfigSchema;
use App\Components\Config\Schema\Node\ArrayNode;
use App\Components\Config\Schema\Node\BooleanNode;
use App\Components\Config\Schema\Node\IntegerNode;
use App\Components\Config\Schema\Node\ObjectNode;
use App\Components\Config\Schema\Node\StringNode;
use App\Components\Config\Schema\Prototype\ObjectPrototype;
use PHPUnit\Framework\TestCase;

class ConfigSchemaTest extends TestCase
{
    public function testGetNode(): void
    {
        $schema = new ConfigSchema(
            new BooleanNode('is_test'),
            new StringNode('test_string'),
            new ObjectNode('parameters', new ConfigSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new ConfigSchema(
                    new StringNode('parameter4'),
                    new ArrayNode('parameter5', new ObjectPrototype(
                        new StringNode('parameter6'),
                        new IntegerNode('parameter7')
                    )),
                )),
            )),
        );

        self::assertEquals('parameters', $schema->getNode('parameters')->getKey());
    }

    public function testExceptionOnDuplicatedKeys(): void
    {
        $this->expectException(InvalidSchemaException::class);

        new ConfigSchema(
            new BooleanNode('duplicate'),
            new StringNode('duplicate'),
            new ObjectNode('parameters', new ConfigSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new ConfigSchema(
                    new StringNode('parameter4'),
                    new ArrayNode('parameter5', new ObjectPrototype(
                        new StringNode('parameter6'),
                        new IntegerNode('parameter7')
                    )),
                )),
            )),
        );
    }
}
