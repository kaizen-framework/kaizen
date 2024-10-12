# Config component

The config component allow to build a schema that represent the configuration of your application with a validation of
type and rules (required, default value, etc.)

## Usage

To create your schema you can use the `ConfigSchemaBuilder`, it allow to build your schema with methods that represent
the different node and their rules validations e.g.

```php
$schemaBuilder = new \Kaizen\Components\Config\Schema\ConfigSchemaBuilder();

$schema = $schemaBuilder
    ->boolean('booleanNode')
        ->required()
        ->defaultValue(false)
        ->buildNode()
    ->integer('integerNode')
        ->min(1)
        ->max(10)
        ->defaultValue(3)
        ->buildNode()
    ->array('arrayNode')
        ->withPrototype(new \Kaizen\Components\Config\Schema\Prototype\IntegerPrototype())
        ->buildNode()
    ->array('arrayObjectNode')
        ->withPrototype(new \Kaizen\Components\Config\Schema\Prototype\ObjectPrototype(
            new \Kaizen\Components\Config\Schema\Node\StringNode('arrayObjectStringNode'),
            new \Kaizen\Components\Config\Schema\Node\BooleanNode('arrayObjectBooleanNode'),
        ))
    ->child('objectNode')
        ->float('childFloatNode')
            ->max(10.5)
            ->required()
            ->buildNode()
        ->string('childStringNode')
            ->required()
            ->buildNode()
        ->build()
    ->build()
```

with this example let's take a yaml config that satisfies this schema :

```yaml
booleanNode: true
integerNode: 1
arrayNode: # Or arrayNode: [1,2,3]
  - 1
  - 2
  - 3
arrayObjectNode:
  - {arrayObjectStringNode: a string, arrayObjectBooleanNode: false}
  - {arrayObjectStringNode: an other string, arrayObjectBooleanNode: true}
  # ...
objectNode:
  childFloatNode: 5.5
  childStringNode: a string
```

Then use the `ConfigLocator` to locate your config files, e.g.

```php
$configLocator = new ConfigLocator('path/of/the/config/files', ['supported', 'extensions']);

$config = $configLocator->locate();
```

Once your schema is built you can use the `ConfigProcessor` To validate your schema

```php
$configProcessor = new \Kaizen\Components\Config\Processor\ConfigProcessor();
$configProcessor->processConfig($realConfig, $schema);
```

The processor will automatically set the default values for the keys not present in your configuration.

- ### Nodes

    - scalarNode (int, float, boolean, string, null)
    - numericNode (int, float)
    - intNode
    - floatNode
    - stringNode
    - booleanNode
    - numberNode (int and float)
    - enumNode
    - arrayNode (can include prototype to define the shape c.f. )

- ### The prototypes

The prototype allow to define a shape that determine which values are expected in an iterable node e.g.

```php
$node = new ArrayNode('node key', new ScalarPrototype());
```

This way the values in the array can only be scalar.

There are already enough built-in prototype to satisfy most of the common use cases, however if you need custom 
validation, you can create your own by creating a class that implement the `PrototypeInterface`

```php
class CustomPrototype implements \Kaizen\Components\Config\Schema\Prototype\PrototypeInterface
{
    public function validateArray(array $array): void
    {
        // perform your custom validation
        // throw an InvalidNodeTypeException if the conditions are not met
    }
}

$customArrayNodePrototype = new \Kaizen\Components\Config\Schema\Node\ArrayNode('node key', new CustomPrototype())
```

  #### Built-in prototype :
  - **TuplePrototype**

The shape of the array must respect the types and the order e.g.
```php
$customArrayNodePrototype = new \Kaizen\Components\Config\Schema\Node\ArrayNode('node key', new \Kaizen\Components\Config\Schema\Prototype\TuplePrototype(
    \Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum::BOOLEAN,
    \Kaizen\Components\Config\Schema\Prototype\TupleTypesEnum::INTEGER,
));

// Valid array
[true, 123]

// Invalid arrays
[123, true];
[true, '123']
[true, 12.3]
[true, true]
```

  - **StringPrototype** 

The array should contain only strings

  - **IntegerPrototype**

The array should contains only integer

  - **ObjectPrototype**

The array should contain objects e.g.

```php
$node = new \Kaizen\Components\Config\Schema\Node\ArrayNode('node_key', new \Kaizen\Components\Config\Schema\Prototype\ObjectPrototype(
    new \Kaizen\Components\Config\Schema\Node\StringNode('node_sub_key_1'),
    new \Kaizen\Components\Config\Schema\Node\IntegerNode('node_sub_key_2'),
))

// Valid array
[
    ['node_sub_key_1' => 'value', 'node_sub_key_2' => 123]
];

// Invalid arrays
[
    ['node_sub_key_11' => 'value', 'node_sub_key_2' => 123]
];
[
    ['node_sub_key_1' => true, 'node_sub_key_2' => 123]
];
```

```yaml
# valid yaml
node_key:
  - {node_sub_key_1: value, node_sub_key_2: 123}

# invalid yaml
node_key:
  - {node_sub_key_11: value, node_sub_key_2: 123}

# invalid yaml
node_key:
  - {node_sub_key_1: true, node_sub_key_2: 123}

```

  - **ScalarPrototype**

The array should contain only scalar values (string, integer, boolean, float, null)

