<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

interface ConfigWriterInterface
{
    public function writeConfig(string $targetFile): void;
}
