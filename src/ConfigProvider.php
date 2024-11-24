<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            "dependencies" => $this->getDependencies(),
            "listeners"    => $this->getListeners(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            "aliases"    => [],
            "delegators" => [],
            'factories'  => [
                ConfigManager::class => ConfigManagerFactory::class,
            ],
        ];
    }

    public function getListeners(): array
    {
        return [
            [
                'listener' => ConfigManager::class,
                'priority' => -100
            ]
        ];
    }
}