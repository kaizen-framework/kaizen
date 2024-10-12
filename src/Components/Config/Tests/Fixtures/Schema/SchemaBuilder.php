<?php

declare(strict_types=1);

namespace Kaizen\Components\Config\Tests\Fixtures\Schema;

use Kaizen\Components\Config\Schema\ConfigSchemaBuilderInterface;

class SchemaBuilder implements ConfigSchemaBuilderInterface
{
    public function schema(): array
    {
        return [];
    }
}