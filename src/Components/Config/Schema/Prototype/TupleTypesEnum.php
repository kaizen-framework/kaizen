<?php

namespace Kaizen\Components\Config\Schema\Prototype;

enum TupleTypesEnum: string
{
    case STRING = 'string';
    case INTEGER = 'int';
    case FLOAT = 'float';
    case BOOLEAN = 'bool';
    case SCALAR = 'scalar';
}
