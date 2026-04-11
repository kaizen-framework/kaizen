# Config Component

The Config component allows you to build a schema that represents your application's configuration with validation for
types and rules (required fields, default values, etc.).

## Table of Contents

- [Usage](#usage)
- [Nodes](#nodes)
    - [Prototypes](#prototypes)
        - [Built-in Prototypes](#built-in-prototypes)
- [Advanced Validation](#advanced-validation)
    - [Closure Validator](#closure-validator)
    - [Conditional Validation](#conditional-validation)
    - [Boolean Shorthand](#boolean-shorthand)
    - [Custom Constraint Validators](#custom-constraint-validators)
- [Error Handling](#error-handling)

## Usage

To create your schema you need to create a class that implements `ConfigInterface`.
You can use the `NodeListSchemaBuilder` to construct your schema with methods that represent different nodes and their validation rules. For example:

```php
class Configuration implements \Kaizen\Components\Config\ConfigInterface
{
    public function getConfigSchema(): \Kaizen\Components\Config\Schema\NodeListSchema
    {
        $schemaBuilder = new \Kaizen\Components\Config\Schema\NodeListSchemaBuilder();

        return $schemaBuilder
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
                ->withPrototype()->integer()
                ->buildNode()
            ->array('arrayObjectNode')
                ->withPrototype()->object(static fn (NodeListSchemaBuilder $builder) => $builder
                    ->string('arrayObjectStringNode')->buildNode()
                    ->boolean('arrayObjectBooleanNode')->buildNode()
                    ->build()
                )->buildNode()
            ->child('objectNode')
                ->float('childFloatNode')
                    ->max(10.5)
                    ->required()
                    ->buildNode()
                ->string('childStringNode')
                    ->required()
                    ->buildNode()
                ->buildChildNode()
            ->build();
    }
}
```

Let's take a YAML config that satisfies this schema:

```yaml
booleanNode: true
integerNode: 1
arrayNode: # Or arrayNode: [1,2,3]
  - 1
  - 2
  - 3
arrayObjectNode:
  - { arrayObjectStringNode: a string, arrayObjectBooleanNode: false }
  - { arrayObjectStringNode: another string, arrayObjectBooleanNode: true }
objectNode:
  childFloatNode: 5.5
  childStringNode: a string
```

To locate your config files, use the `ConfigLocator`:

```php
// The second argument is an array of parsers that transform the loaded file into a usable PHP array
$configLocator = new ConfigLocator('root/path/of/the/config/files', [new \Kaizen\Components\Config\Parser\YamlParser()]);
$config = $configLocator->locate('some_file.yaml');
```

> **Note:** Make sure to load only files that are supported by the parsers you've added to the `ConfigLocator` constructor.

Currently, only a YAML parser is available. However, you can create your own parser by implementing `ParserInterface`:

```php
class CustomParser implements \Kaizen\Components\Config\Parser\ParserInterface
{
    public function parse(string $fileContent): array
    {
        // Logic to parse your document
    }

    public function supports(string $path): bool
    {
        // Check whether the parser should handle this file or not
        return pathinfo($path, PATHINFO_EXTENSION) === 'desired_extension';
    }
}
```

Once your schema is built, use the `ConfigProcessor` to validate and process your configuration:

```php
$configProcessor = new \Kaizen\Components\Config\Processor\ConfigProcessor();
$config = $configProcessor->processConfig($config, new Configuration());
```

The processor will apply default values, enforce required fields, validate types and rules across all nesting levels, and detect duplicated schema keys. Your configuration is now extracted and validated!

## Nodes

The following node types are available to describe your configuration values:

- `scalar` — accepts any scalar value (`int`, `float`, `boolean`, `string`, `null`)
- `integer` — accepts integers only
- `float` — accepts floats only
- `string` — accepts strings only
- `boolean` — accepts booleans only
- `numeric` — accepts integers and floats
- `array` — accepts an array, optionally with a prototype to define the expected shape

### Prototypes

Prototypes define the expected values inside an `array` node. They are set via the fluent `->withPrototype()` API on the builder:

```php
$schemaBuilder->array('myNode')
    ->withPrototype()->scalar()
    ->buildNode();
```

This ensures the array contains only scalar values.

While there are built-in prototypes for common use cases, you can create custom validations by extending `AbstractPrototype` and passing it directly via `->setPrototype()`:

```php
class CustomPrototype extends \Kaizen\Components\Config\Schema\Prototype\AbstractPrototype
{
    public function isValueSatisfiesPrototypeSpecification(
        array $values,
        ValidationContext $validationContext
    ): ValidationResult {
        // Perform your custom validation and return a ValidationResult
    }

    public function normalize(array $array): array
    {
        // Optionally transform the values before validation
        return $array;
    }
}

$schemaBuilder->array('myNode')
    ->setPrototype(new CustomPrototype())
    ->buildNode();
```

#### Built-in Prototypes

- **`->withPrototype()->scalar()`** — the array must contain only scalar values (`string`, `integer`, `boolean`, `float`, `null`)
- **`->withPrototype()->string()`** — the array must contain only strings
- **`->withPrototype()->integer()`** — the array must contain only integers
- **`->withPrototype()->float()`** — the array must contain only floats
- **`->withPrototype()->numeric()`** — the array must contain only integers or floats
- **`->withPrototype()->tuple()`** — the array must respect specific types in a specific order

```php
$schemaBuilder->array('myNode')
    ->withPrototype()->tuple(TupleTypesEnum::BOOLEAN, TupleTypesEnum::INTEGER)
    ->buildNode();

// Valid
[true, 123]

// Invalid
[123, true]   // wrong order
[true, '123'] // wrong type
[true, 12.3]  // float is not integer
```

- **`->withPrototype()->object()`** — the array must contain objects matching a defined schema, described via a closure:

```php
$schemaBuilder->array('databases')
    ->withPrototype()->object(static fn (NodeListSchemaBuilder $builder) => $builder
        ->string('host')->required()->buildNode()
        ->integer('port')->defaultValue(5432)->buildNode()
        ->build()
    )
    ->buildNode();
```

```yaml
# Valid YAML
databases:
  - { host: localhost, port: 3306 }
  - { host: remotehost, port: 5432 }

# Invalid — wrong type for port
databases:
  - { host: localhost, port: "3306" }

# Invalid — unknown key
databases:
  - { host: localhost, port: 3306, unknown: value }
```

## Advanced Validation

Beyond built-in type checking, the Config component provides a constraint system to express custom validation logic on any node.

### Closure Validator

The simplest way to add custom validation is via `->validateClosure()`, which accepts a closure and an error message:

```php
$schemaBuilder
    ->string('environment')
        ->validateClosure(
            static fn (string $value) => in_array($value, ['dev', 'staging', 'prod'], true),
            'The environment must be one of: dev, staging, prod.'
        )
        ->buildNode();
```

### Conditional Validation

Use `->when()` to apply validation rules only when a condition is met. This is useful for expressing dependencies between fields.

**Fail unconditionally when the condition is true:**

```php
$schemaBuilder
    ->boolean('isSecure')
        ->when(static fn (bool $value) => false === $value)
        ->thenFail('The connection must be secured.')
        ->buildNode();
```

**Run a further check when the condition is true:**

```php
$schemaBuilder
    ->string('mode')
        ->when(static fn (string $value) => 'cluster' === $value)
        ->then(
            static fn (string $value, NodeListSchema $schema) => null !== $schema->getNode('replicaCount'),
            'The "replicaCount" node is required when mode is "cluster".'
        )
        ->buildNode();
```

The `->then()` closure receives both the current node value and the full `NodeListSchema`, allowing cross-field validation.

### Boolean Shorthand

For boolean nodes specifically, `->ifTrue()` is a convenient shorthand for `->when(fn ($v) => true === $v)`:

```php
$schemaBuilder
    ->boolean('enableCache')
        ->ifTrue()
        ->thenFail('Caching is not supported in this environment.')
        ->buildNode();
```

### Custom Constraint Validators

For reusable validation logic, implement `NodeConstraintValidatorInterface` and register it via `->customValidation()`:

```php
class AllowedEnvironmentValidator implements \Kaizen\Components\Config\Schema\Constraint\NodeConstraintValidatorInterface
{
    private const ALLOWED = ['dev', 'staging', 'prod'];

    public function validate(mixed $value, Node $node, NodeListSchema $nodeListSchema): bool
    {
        return in_array($value, self::ALLOWED, true);
    }

    public function getMessage(): string
    {
        return sprintf('The environment must be one of: %s.', implode(', ', self::ALLOWED));
    }
}

$schemaBuilder
    ->string('environment')
        ->customValidation(new AllowedEnvironmentValidator())
        ->buildNode();
```

Multiple constraints can be chained on the same node — all of them will be evaluated:

```php
$schemaBuilder
    ->string('slug')
        ->length(3) // You can pass min and/or max accept null e.g. length(null, 10) or length(max: 10) this mean the length of the string should be between 0 and 10 
        ->validateClosure(
            static fn (string $v) => preg_match('/^[a-z0-9-]+$/', $v) === 1,
            'The slug must contain only lowercase letters, numbers and hyphens.'
        )
        ->buildNode();
```
