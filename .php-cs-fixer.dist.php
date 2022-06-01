<?php

require_once 'tools/php-cs-fixer/vendor/autoload.php';

$header = <<<'EOF'
Copyright (c) 2022 Luyanda Siko <sikoluyanda@gmail.com>,
All rights reserved.

This file is part of Beyond Capable Platform.

This program and the accompanying materials are made available under
the terms of the BSD 2-Clause License which accompanies this
distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/core',
        __DIR__ . '/tests',
    ])
    ->exclude([
        __DIR__ . '/tools',
        __DIR__ . '/data',
        __DIR__ . '/public',
        __DIR__ . '/var',
        __DIR__ . '/vendor',
        __DIR__ . '/config',
        __DIR__ . '/build',
        __DIR__ . '/assets',
        __DIR__ . '/templates',
        __DIR__ . '/node_modules'
    ]);

$config = new PhpCsFixer\Config();

return $config
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRules([
        '@PSR12' => true,
        'full_opening_tag' => false,
        'header_comment' => ['header' => $header],
        'array_syntax' => ['syntax' => 'short'],
        'ternary_operator_spaces' => true,
        PhpCsFixerCustomFixers\Fixer\NoLeadingSlashInGlobalNamespaceFixer::name() => true,
        PhpCsFixerCustomFixers\Fixer\PhpdocNoSuperfluousParamFixer::name() => true,
    ])
    ->setFinder($finder);