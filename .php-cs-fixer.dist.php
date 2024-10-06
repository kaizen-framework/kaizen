<?php

$finder = PhpCsFixer\Finder::create()->in([__DIR__.'/src']);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'php_unit_test_class_requires_covers' => true,
])->setFinder($finder);