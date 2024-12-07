<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager\Resources;

class FooConfigProvider implements ConfigProviderInterface
{
    public const CONFIG_MANAGER_TARGET_FILE = 'foo.global.php';

    public function __invoke(): array
    {
        return [
            static::class => $this->getAxleusConfig(),
        ];
    }

    public function getAxleusConfig(): array
    {
        return [
            'baz' => 'bat',
        ];
    }
}
