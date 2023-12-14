<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()->in(__DIR__ . '/src');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    '@PER' => true,
    '@PHP82Migration' => true,
    '@PhpCsFixer' => true,
])
    ->setFinder($finder);
