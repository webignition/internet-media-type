<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'concat_space' => [
        'spacing' => 'one',
    ],
    'trailing_comma_in_multiline' => false,
    'php_unit_internal_class' => false,
    'php_unit_test_class_requires_covers' => false,
])->setFinder($finder);
