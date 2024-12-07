<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager;

use Axleus\ConfigManager\ConfigManager;
use Axleus\ConfigManager\Event\ConfigEvent;
use AxleusTest\ConfigManager\Resources\FooConfigProvider;
use PHPUnit\Framework\TestCase;

use function file_exists;
use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;

final class ConfigManagerTest extends TestCase
{
    private const CACHE_FILE = '/config-cache.php';
    private const CACHE_KEY  = 'config_cache_path';
    private string $dir;
    private string $cacheFile;
    private string $targetFile;
    private array $config = [
        'debug' => false,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/config_manager';
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }
        $this->cacheFile  = $this->dir . self::CACHE_FILE;
        $this->targetFile = $this->dir;
        $provider         = new Resources\FooConfigProvider();
        $this->config[Resources\FooConfigProvider::class] = $provider->getAxleusConfig();
        file_put_contents($this->cacheFile, $provider->getAxleusConfig());
        $this->config[self::CACHE_KEY] = $this->cacheFile;
    }

    protected function tearDown(): void
    {
        if (file_exists($this->cacheFile) && is_file($this->cacheFile)) {
            @unlink($this->cacheFile);
        }
        if (file_exists($this->targetFile) && is_file($this->targetFile)) {
            @unlink($this->targetFile);
        }
        if (is_dir($this->dir)) {
            @rmdir($this->dir);
        }
    }

    public function testConfigManagerCanBustCache(): void
    {
        $configManager = new ConfigManager($this->config);
        $eventResult   = $configManager->onBustCache(new ConfigEvent());
        self::assertFileDoesNotExist($this->cacheFile);
        self::assertTrue($eventResult === true);
    }

    public function testConfigManagerCanWriteUpdatedConfig(): void
    {
        $configManager    = new ConfigManager($this->config);
        $configEvent      = new ConfigEvent(null, FooConfigProvider::class);
        $this->targetFile = $this->targetFile . '/' . FooConfigProvider::CONFIG_MANAGER_TARGET_FILE;
        $configEvent->setTargetFile($this->targetFile);
        $configEvent->setUpdatedConfig(include 'config/test.global.php');
        $eventResult   = $configManager->onSaveConfig($configEvent);
        self::assertFileExists($configEvent->getTargetFile());
        $writtenConfig = include $this->targetFile;
        self::assertSame(
            [
                FooConfigProvider::class => [
                    'test_key' => 'test_value',
                    'baz'      => 'bat',
                ],
            ], $writtenConfig);
        self::assertTrue($eventResult === true);
    }

    // todo: split this test out
    public function testConfigManagerStopsPropagationWhenInDevelopmentMode(): void
    {
        $this->config['debug'] = true;
        $configManager         = new ConfigManager($this->config);
        $configEvent           = new ConfigEvent(null, FooConfigProvider::class);
        $eventResult           = $configManager->onSaveConfig($configEvent);
        self::assertTrue($configEvent->propagationIsStopped() === true);
    }
}
