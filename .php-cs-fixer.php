<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration' => true,
        'yoda_style' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
        'ordered_types' => true,
    ])
    ->setFinder((new PhpCsFixer\Finder())->in(__DIR__ . '/src'));