<?php

declare(strict_types=1);

namespace AxleusTestResource\ConfigManager;

interface ConfigProviderInterface
{
    public const CONFIG_MANAGER_TARGET_FILE = '';
    public function getAxleusConfig(): array;
}
