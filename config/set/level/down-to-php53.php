<?php

declare (strict_types=1);
namespace RectorPrefix20220120;

use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Set\ValueObject\DowngradeSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(\Rector\Set\ValueObject\DowngradeLevelSetList::DOWN_TO_PHP_54);
    $containerConfigurator->import(\Rector\Set\ValueObject\DowngradeSetList::PHP_54);
};
