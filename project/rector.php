<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony64\Rector\Class_\ChangeRouteAttributeFromAnnotationSubnamespaceRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return static function (RectorConfig $rectorConfig): void {

    // @see : https://getrector.com/blog/5-common-mistakes-in-rector-config-and-how-to-avoid-them
    // register single rule
    $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->skip([
        __DIR__ . '/migrations',
        ChangeRouteAttributeFromAnnotationSubnamespaceRector::class,
    ]);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets with your IDE
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
        SetList::PHP_82,
        SymfonySetList::SYMFONY_64,
        SymfonySetList::SYMFONY_CODE_QUALITY,
//        SetList::NAMING,
//        SetList::PRIVATIZATION,
    ]);
};
