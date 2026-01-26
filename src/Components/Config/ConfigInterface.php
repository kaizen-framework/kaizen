<?php

declare(strict_types=1);

namespace Kaizen\Components\Config;

use Kaizen\Components\Config\Schema\NodeListSchema;

interface ConfigInterface
{
    public function getConfigSchema(): NodeListSchema;
}