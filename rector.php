<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $config): void
{
    $config->sets([
        LevelSetList::UP_TO_PHP_81,
        SymfonySetList::SYMFONY_54,
    ]);
};
