<?php

declare(strict_types=1);

namespace App\Components\Config\Tests\Fixtures\Schema;

use App\Components\Config\Schema\ConfigSchemaBuilderInterface;

class InvalidSchemaBuilder implements ConfigSchemaBuilderInterface
{
    public function schema(): array
    {
        return [];
    }
}