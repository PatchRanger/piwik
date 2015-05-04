<?php
$finder = Symfony\CS\Finder\DefaultFinder::create();
$finder->in([
    __DIR__ . '/core'
]);

$config = Symfony\CS\Config\Config::create();
$config->setUsingCache(true);
$config->finder($finder);
$config->level(Symfony\CS\FixerInterface::PSR2_LEVEL);
$config->fixers([
    //to still support php 5.3
    'long_array_syntax',
]);

return $config;
