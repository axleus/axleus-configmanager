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
        self::assertEquals($path, $configEvent->getPath());
    }

    public function testConfigEventCanSetTargetFile(): void
    {
        $targetFile  = 'test.global.php';
        $configEvent = new ConfigEvent();
        $configEvent->setTargetFile($targetFile);
        self::assertEquals($targetFile, $configEvent->getTargetFile());
    }

    public function testConfigEventCanSetUpdatedConfig(): void
    {
        $updatedConfig = ['some_key' => 'some_value'];
        $configEvent   = new ConfigEvent();
        $configEvent->setUpdatedConfig($updatedConfig);
        self::assertEquals($updatedConfig, $configEvent->getUpdatedConfig());
    }

    public function testConfigEventCanSetTargetCache(): void
    {
        $targetCache = '/some/path/to/cache.php';
        $configEvent = new ConfigEvent();
        $configEvent->setTargetCache($targetCache);
        self::assertEquals($targetCache, $configEvent->getTargetCache());
    }
}
