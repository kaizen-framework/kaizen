<?php

declare(strict_types=1);

namespace App\Components\Config\Tests\Processor;

use App\Components\Config\Exception\ConfigProcessingException;
use App\Components\Config\Exception\InvalidNodeTypeException;
use App\Components\Config\Exception\NodeNotFoundException;
use App\Components\Config\Processor\ConfigProcessor;
use App\Components\Config\Schema\ConfigSchema;
use App\Components\Config\Schema\Node\BooleanNode;
use App\Components\Config\Schema\Node\ObjectNode;
use App\Components\Config\Schema\Node\ObjectVariableNode;
use App\Components\Config\Schema\Node\ScalarNode;
use App\Components\Config\Schema\Node\StringNode;
use App\Components\Config\Schema\Prototype\StringPrototype;
use PHPUnit\Framework\TestCase;

class ConfigProcessorTest extends TestCase
{
    public function testDefaultValuesAreAppendForMissingConfig(): void
    {
        $config = [
            'node' => 'test'
        ];

        $schema = new ConfigSchema(
            (new StringNode('node'))->defaultValue('default'),
            (new ScalarNode('node2'))->defaultValue('default_value')
        );

        $configProcessor = new ConfigProcessor();

        $configProcessor->processConfig($config, $schema);

        self::assertEquals('test', $config['node']);
        self::assertEquals('default_value', $config['node2']);
    }

    public function testNodeProcessValueIsCalled(): void
    {
        $configSchema = $this->createMock(ConfigSchema::class);

        $node = $this->createMock(StringNode::class);
        $node->method('getKey')->willReturn('node');
        $node->expects(self::once())->method('processValue');

        $configSchema->method('getNode')->willReturn($node);

        $config = [
            'node' => 'test'
        ];

        $configProcessor = new ConfigProcessor();
        $configProcessor->processConfig($config, $configSchema);
    }

    public function testProcessConfig(): void
    {
        $this->expectNotToPerformAssertions();
        $configProcessor = new ConfigProcessor();

        $config = $this->getConfig();
        $configProcessor->processConfig($config, $this->getSchema());
    }

    public function invalidConfigProvider(): \Iterator
    {
        yield 'With invalid key' => [[
            'parameter' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%'
                    ]
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\\Service\\Test' => [
                    'lazy' => true,
                ],
            ]
        ], NodeNotFoundException::class];

        yield 'With invalid type' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%'
                    ]
                ],
                'App\\' => [
                    'autowire' => 123,
                ],
                'App\\Service\\Test' => [
                    'lazy' => true,
                ],
            ]
        ], InvalidNodeTypeException::class];

        yield 'With missing required field' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
        ], ConfigProcessingException::class];

        yield 'With missing required field #2' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
            'services' => [
                '_default' => [
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\\Service\\Test' => [
                    'lazy' => true,
                ],
            ]
        ], ConfigProcessingException::class];

        yield 'With invalid object node schema' => [[
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%'
                    ]
                ],
                'App\\' => [
                    'invalid_node' => true,
                ],
                'App\\Service\\Test' => [
                    'lazy' => true,
                ],
            ]
        ], NodeNotFoundException::class];
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function testWithInvalidConfig(array $config, string $exceptionClass): void
    {
        $configProcessor = new ConfigProcessor();

        $this->expectException($exceptionClass);
        $configProcessor->processConfig($config, $this->getSchema());
    }

    private function getConfig(): array
    {
        return [
            'parameters' => [
                'parameter1' => 'value1',
                'parameter2' => 2,
                'parameter3' => true,
                'parameter4' => 1.2,
                'parameter5' => [
                    [
                        'key1' => 'value',
                        'key2' => 21,
                    ]
                ]
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%'
                    ]
                ],
                'App\\' => [
                    'autowire' => true,
                ],
                'App\\Service\\Test' => [
                    'lazy' => true,
                ],
            ]
        ];
    }

    private function getSchema(): ConfigSchema
    {
        return new ConfigSchema(
            new ObjectVariableNode('parameters'),
            (new ObjectNode('services', new ConfigSchema(
                new ObjectNode('_default', new ConfigSchema(
                    (new ObjectVariableNode('bind', new StringPrototype()))->required(),
                )),
                new ObjectNode('*', new ConfigSchema(
                    (new BooleanNode('autowire'))->defaultValue(true),
                    (new BooleanNode('lazy'))->defaultValue(false),
                )),
            )))->required()
        );
    }
}