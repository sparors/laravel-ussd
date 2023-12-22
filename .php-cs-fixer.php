<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration' => true,
    ])
    ->setFinder((new PhpCsFixer\Finder())->in(__DIR__ . '/src'));