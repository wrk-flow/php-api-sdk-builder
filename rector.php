<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\SimplifyBoolIdenticalTrueRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\BooleanNot\BooleanInBooleanNotRuleFixerRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function (RectorConfig $config): void {
    $config->paths([__DIR__ . '/src', __DIR__ . '/tests']);
    $config->phpVersion(PhpVersion::PHP_81);

    // Define what rule sets will be applied
    $config->import(LevelSetList::UP_TO_PHP_81);
    $config->import(SetList::CODE_QUALITY);
    $config->import(SetList::CODING_STYLE);
    $config->importNames();

    $config->ruleWithConfiguration(AddVoidReturnTypeWhereNoReturnRector::class, [
        AddVoidReturnTypeWhereNoReturnRector::USE_PHPDOC => false,
    ]);
    $config->ruleWithConfiguration(
        rectorClass: BooleanInBooleanNotRuleFixerRector::class,
        configuration: [
            BooleanInBooleanNotRuleFixerRector::TREAT_AS_NON_EMPTY => false,
        ]
    );

    $config->skip([
        AddVoidReturnTypeWhereNoReturnRector::class => [__DIR__ . '/src/Testing/Response.php'],
        // Collides with ECS ClassAttributesSeparationFixer
        NewlineAfterStatementRector::class,
        SimplifyBoolIdenticalTrueRector::class,
    ]);
};
