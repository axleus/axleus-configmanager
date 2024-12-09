<?php

declare(strict_types=1);

namespace AxleusTestResource\ConfigManager;

use Axleus\Core\ConfigProviderInterface;

class FooConfigProvider implements ConfigProviderInterface
{
    public const TARGET_FILE = 'foo.global.php';

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
            'key_old' => 'key_old',
        ];
    }
}
