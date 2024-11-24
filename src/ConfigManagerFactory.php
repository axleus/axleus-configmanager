<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

use Psr\Container\ContainerInterface;

final class ConfigManagerFactory
{
    public function __invoke(ContainerInterface $container): ConfigManager
    {
        return new ConfigManager($container->get('config'));
    }
}
