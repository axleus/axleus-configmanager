<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager\Event;

use Axleus\ConfigManager\Event\ConfigEvent;
use PHPUnit\Framework\TestCase;

final class ConfigEventTest extends TestCase
{
    public function testCanSetPath(): void
    {
        $path = '/some/path';
        $configEvent = new ConfigEvent();
        $configEvent->setPath($path);
        $returned = $configEvent->getPath();
        self::assertEquals($path, $returned);
    }

    public function testConfigEventCanSetTargetFile(): void
    {
        $targetFile  = 'test.global.php';
        $configEvent = new ConfigEvent();
        $configEvent->setTargetFile($targetFile);
        $returned = $configEvent->getTargetFile();
        self::assertEquals($targetFile, $returned);
    }

    public function testConfigEventCanSetUpdatedConfig(): void
    {
        $updatedConfig = ['some_key' => 'some_value'];
        $configEvent   = new ConfigEvent();
        $configEvent->setUpdatedConfig($updatedConfig);
        $returned = $configEvent->getUpdatedConfig();
        self::assertEquals($updatedConfig, $returned);
    }

    public function testConfigEventCanSetTargetCache(): void
    {
        $targetCache = '/some/path/to/cache.php';
        $configEvent = new ConfigEvent();
        $configEvent->setTargetCache($targetCache);
        $returned = $configEvent->getTargetCache();
        self::assertEquals($targetCache, $returned);
    }
}
