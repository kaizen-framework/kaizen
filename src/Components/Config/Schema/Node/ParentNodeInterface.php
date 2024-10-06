<?php

namespace App\Components\Config\Schema\Node;

use App\Components\Config\Schema\ConfigSchema;

interface ParentNodeInterface
{
    public function getChildren(): ConfigSchema;
}