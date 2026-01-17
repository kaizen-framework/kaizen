<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\Constant;

/**
 * The available environment values
 */
enum EnvironmentType: string
{
    case DEVELOPMENT = 'dev';
    case TEST = 'test';
    case PRODUCTION = 'prod';
}