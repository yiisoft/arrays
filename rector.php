<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Yiisoft\CodeStyle\Rector\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withCache(__DIR__ . '/runtime/rector')
    ->withPhpSets(php81: true)
    ->withSets([
        SetList::YII_CORE,
    ]);
