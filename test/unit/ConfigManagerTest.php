<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager;

use Axleus\ConfigManager\ConfigManager;
use PHPUnit\Framework\TestCase;

final class ConfigManagerTest extends TestCase
{
    public function testTestAreRunning(): void
    {
        $configManager = new ConfigManager([]);
        $this->assertInstanceOf(ConfigManager::class, $configManager);
    }
}
