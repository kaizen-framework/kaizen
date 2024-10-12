<?php

$finder = PhpCsFixer\Finder::create()->in([__DIR__.'/src/Components/**/*']);

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'php_unit_test_class_requires_covers' => true,
    'phpdoc_to_comment' => [
        'allow_before_return_statement' => true,
    ],
])->setFinder($finder);