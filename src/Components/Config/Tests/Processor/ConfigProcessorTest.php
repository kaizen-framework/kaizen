<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Processor;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Exception\ConfigProcessingException;
use Kaizen\Components\Config\Processor\ConfigProcessor;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\Node\BooleanNode;
use Kaizen\Components\Config\Schema\Node\IntegerNode;
use Kaizen\Components\Config\Schema\Node\ObjectNode;
use Kaizen\Components\Config\Schema\Node\ObjectVariableNode;
use Kaizen\Components\Config\Schema\Node\StringNode;
use Kaizen\Components\Config\Schema\Prototype\ObjectPrototype;
use Kaizen\Components\Config\Schema\Prototype\StringPrototype;
use Kaizen\Components\Config\Schema\Validation\Numeric\MinConstraintValidator;
use Kaizen\Components\Config\Schema\Validation\String\LengthConstraintValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ConfigProcessor::class)]
class ConfigProcessorTest extends TestCase
{
    public function testDefaultValuesAreAppendForMissingConfig(): void
    {
        $config = [
            'node1' => 'test',
            'node2' => 'test',
            'node3' => 'test',
            'node6' => [
                'param1' => 'test',
            ],
        ];

        $nodeList = new NodeListSchema(
            new StringNode('node1')->addConstraintValidator(new LengthConstraintValidator(4, null)),
            new StringNode('node2'),
            new StringNode('node3'),
            new StringNode('node4'),
            new StringNode('node5')->defaultValue('default_value'),
            new ObjectNode('node6', new NodeListSchema(
                new StringNode('param1')->required(),
                new StringNode('param2'),
                new StringNode('param3')->defaultValue('default_value'),
            )),
            new ObjectNode('node7', new NodeListSchema(
                new StringNode('param4'),
                new StringNode('param5')->defaultValue('default_value'),
            )),
            new ObjectNode('node8', new NodeListSchema(
                new StringNode('param6'),
                new StringNode('param7')->defaultValue('default_value'),
            ))->defaultValue([
                'param6' => 'object_default_value',
            ]),
        );

        $expectedResult = [
            'node1' => 'test',
            'node2' => 'test',
            'node3' => 'test',
            'node5' => 'default_value',
            'node6' => [
                'param1' => 'test',
                'param3' => 'default_value',
            ],
            'node8' => [
                'param6' => 'object_default_value',
                'param7' => 'default_value',
            ],
        ];

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($nodeList);

        $configProcessor = new ConfigProcessor();

        $actual = $configProcessor->processConfig($config, $configInterface);

        self::assertEquals($expectedResult, $actual);
    }

    public function testWithConstraintError(): void
    {
        $config = [
            'node2' => [
                'node4' => 5
            ]
        ];

        /** @var MinConstraintValidator<int> $minConstraint */
        $minConstraint = new MinConstraintValidator(10);

        $nodeListSchema = new NodeListSchema(
            new StringNode('node1'),
            new ObjectNode('node2', new NodeListSchema(
                new StringNode('node3'),
                new IntegerNode('node4')->addConstraintValidator($minConstraint),
            ))
        );

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($nodeListSchema);

        $configProcessor = new ConfigProcessor();

        $this->expectException(ConfigProcessingException::class);
        $this->expectExceptionMessage('Invalid configuration at "node2.node4": The value must be greater than or equal to 10');

        $configProcessor->processConfig($config, $configInterface);
    }

    public function testProcessConfig(): void
    {
        $configProcessor = new ConfigProcessor();

        $config = $this->getConfig();

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($this->getSchema())
        ;

        $configProcessor->processConfig($config, $configInterface);
    }

    public static function invalidConfigProvider(): \Iterator
    {
        yield 'With invalid key : "parameter" instead of "parameters"' => [
            [
                'parameter' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
                'services' => [
                    '_default' => [
                        'bind' => [
                            'string $param' => '%parameter1%',
                        ],
                    ],
                    'project_services' => [
                        'App\\' => [
                            'autowire' => true,
                        ],
                        'App\Service\Test' => [
                            'lazy' => true,
                        ],
                    ],
                ],
            ],
            'Invalid configuration key "parameter", allowed keys are: ["parameters", "services"]. Did you mean "parameters" ?'
        ];

        yield 'With invalid type' => [
            [
                'parameters' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
                'services' => [
                    '_default' => [
                        'bind' => [
                            'string $param' => '%parameter1%',
                        ],
                    ],
                    'project_services' => [
                        'App\\' => [
                            'autowire' => 123,
                        ],
                        'App\Service\Test' => [
                            'lazy' => true,
                        ],
                    ],
                ],
            ],
            'Invalid configuration at "services.project_services.App\.autowire": Expected a boolean, but got int.'
        ];

        yield 'With missing required field' => [
            [
                'parameters' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
            ],
            'Invalid configuration at "services": The value is required but was not provided or is null.'
        ];

        yield 'With missing required field #2' => [
            [
                'parameters' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
                'services' => [
                    '_default' => [
                    ],
                    'project_services' => [
                        'App\\' => [
                            'autowire' => true,
                        ],
                        'App\Service\Test' => [
                            'lazy' => true,
                        ],
                    ],
                ],
            ],
            'Invalid configuration at "services._default.bind": The value is required but was not provided or is null.'
        ];

        yield 'With invalid object node schema' => [
            [
                'parameters' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
                'services' => [
                    '_default' => [
                        'bind' => [
                            'string $param' => '%parameter1%',
                        ],
                    ],
                    'project_services' => [
                        'App\\' => [
                            'invalid_node' => true,
                        ],
                        'App\Service\Test' => [
                            'lazy' => true,
                        ],
                    ],
                ],
            ],
            'Invalid configuration at "services.project_services.App\": Unknown "invalid_node", allowed keys are: ["autowire", "lazy"].'
        ];

        yield 'With typo in object key' => [
            [
                'parameters' => [
                    'parameter1' => 'value1',
                    'parameter2' => 2,
                    'parameter3' => true,
                    'parameter4' => 1.2,
                    'parameter5' => [
                        [
                            'key1' => 'value',
                            'key2' => 21,
                        ],
                    ],
                ],
                'services' => [
                    '_default' => [
                        'bind' => [
                            'string $param' => '%parameter1%',
                        ],
                    ],
                    'project_services' => [
                        'App\\' => [
                            'autoire' => true,
                        ],
                        'App\Service\Test' => [
                            'lazy' => true,
                        ],
                    ],
                ],
            ],
            'Invalid configuration at "services.project_services.App\": Unknown "autoire", allowed keys are: ["autowire", "lazy"]. Did you mean "autowire" ?'
        ];
    }

    /**
     * @param array<string, mixed> $config
     */
    #[DataProvider('invalidConfigProvider')]
    public function testWithInvalidConfig(array $config, string $expectedExceptionMessage): void
    {
        $configProcessor = new ConfigProcessor();

        $this->expectException(ConfigProcessingException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($this->getSchema())
        ;

        $configProcessor->processConfig($config, $configInterface);
    }

    public function testWithConfigNotMatchSchema(): void
    {
        $config = ['parent' => 'wrong'];

        $schema = new NodeListSchema(
            new ObjectNode('parent', new NodeListSchema(
                new StringNode('test')
            ))
        );

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($schema);

        $configProcessor = new ConfigProcessor();

        $this->expectException(ConfigProcessingException::class);

        $configProcessor->processConfig($config, $configInterface);
    }

    public function testWithConfigNotMatchChildSchema(): void
    {
        $config = ['parent' => [
            'child' => 123
        ]];

        $schema = new NodeListSchema(
            new ObjectNode('parent', new NodeListSchema(
                new ObjectNode('child', new NodeListSchema(
                    new StringNode('test2')
                ))
            ))
        );

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($schema);

        $configProcessor = new ConfigProcessor();

        $this->expectException(ConfigProcessingException::class);

        $configProcessor->processConfig($config, $configInterface);
    }

    public function testWithDuplicatedKeysInNodeList(): void
    {
        $this->expectException(ConfigProcessingException::class);
        $this->expectExceptionMessage(
            'The provided configuration is invalid, the keys ["duplicate", "parameters.parameter3.parameter4"] are duplicated.'
        );

        $nodeListWithDuplicateKeys = new NodeListSchema(
            new BooleanNode('duplicate'),
            new StringNode('duplicate'),
            new ObjectNode('parameters', new NodeListSchema(
                new StringNode('parameter1'),
                new BooleanNode('parameter2'),
                new ObjectNode('parameter3', new NodeListSchema(
                    new StringNode('parameter4'),
                    new BooleanNode('parameter4'),
                )),
                new ObjectNode('parameter5', new NodeListSchema(
                    new StringNode('parameter6'),
                    new IntegerNode('parameter7'),
                    new IntegerNode('parameter7'),
                ))
            )),
        );

        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->expects($this->once())
            ->method('getConfigSchema')
            ->willReturn($nodeListWithDuplicateKeys);

        $configProcessor = new ConfigProcessor();

        $config = [];

        $this->expectException(ConfigProcessingException::class);
        $this->expectExceptionMessage(
            'The provided configuration is invalid, the keys ["duplicate", "parameters.parameter3.parameter4", "parameters.parameter5.parameter7"] are duplicated.'
        );

        $configProcessor->processConfig($config, $configInterface);
    }

    /**
     * @return array<string, mixed>
     */
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
                    ],
                ],
            ],
            'services' => [
                '_default' => [
                    'bind' => [
                        'string $param' => '%parameter1%',
                    ],
                ],
                'project_services' => [
                    'App\\' => [
                        'autowire' => true,
                    ],
                    'App\Service\Test' => [
                        'lazy' => true,
                    ],
                ],
            ],
        ];
    }

    private function getSchema(): NodeListSchema
    {
        return new NodeListSchema(
            new ObjectVariableNode('parameters'),
            new ObjectNode('services', new NodeListSchema(
                new ObjectNode('_default', new NodeListSchema(
                    new ObjectVariableNode('bind', new StringPrototype())->required(),
                )),
                new ObjectVariableNode('project_services', new ObjectPrototype(
                    new BooleanNode('autowire')->defaultValue(true),
                    new BooleanNode('lazy')->defaultValue(false),
                )),
            ))->required()
        );
    }
}
