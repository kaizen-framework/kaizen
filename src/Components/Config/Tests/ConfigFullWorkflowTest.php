<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests;

use Kaizen\Components\Config\ConfigInterface;
use Kaizen\Components\Config\Loader\ConfigLocator;
use Kaizen\Components\Config\Parser\YamlParser;
use Kaizen\Components\Config\Processor\ConfigProcessor;
use Kaizen\Components\Config\Schema\NodeListSchema;
use Kaizen\Components\Config\Schema\NodeListSchemaBuilder;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
class ConfigFullWorkflowTest extends TestCase
{
    public function testConfigFullWorkflow(): void
    {
        $schema = $this->getConfigInterface();

        $rootPath = sprintf(
            '%s/Fixtures/config/yaml',
            __DIR__
        );

        $configLocator = new ConfigLocator($rootPath, [new YamlParser()]);

        $config = $configLocator->locate('config.yml');

        $configProcessor = new ConfigProcessor();

        $config = $configProcessor->processConfig($config, $schema);

        $expectedConfig = [
            'kaizen' => [
                'config' => [
                    'databases' => [
                        ['database_url' => 'mysql://user:password@localhost:3360/database', 'database_version' => '12.3'],
                        ['database_url' => 'mysql://user:password@localhost:3361/database2', 'database_version' => '1.0.0'],
                    ],
                    'parameters' => [
                        'param1' => 'value1',
                        'param2' => 12,
                        'param3' => 1.2,
                        'param4' => ['1', 2, true],
                        'param5' => ['subkey' => 1],
                        'param6' => ['array', 'param'],
                        'param7' => true,
                        'param8' => null,
                        'param9' => 'string ENUM_CONST',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedConfig, $config);
    }

    private function getConfigInterface(): ConfigInterface
    {
        return new class implements ConfigInterface {
            public function getConfigSchema(): NodeListSchema
            {
                $configSchemaBuilder = new NodeListSchemaBuilder();

                return $configSchemaBuilder
                    ->child('kaizen')
                    ->child('config')
                    ->array('databases')
                    ->withPrototype()->object(static fn (NodeListSchemaBuilder $builder) => $builder
                        ->string('database_url')->pattern('/^[a-zA-Z_-]+:\/\/.*?:.*?@[a-zA-Z_-]+:[0-9]{2,5}\/\w+/')->required()->buildNode()
                        ->string('database_version')->defaultValue('1.0.0')->buildNode()
                        ->build()
                    )->buildNode()
                    ->objectVariable('parameters')->buildNode()
                    ->buildChildNode()
                    ->buildChildNode()
                    ->string('okok')->buildNode()
                    ->build()
                ;
            }
        };
    }
}
