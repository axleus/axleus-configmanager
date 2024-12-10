<?php

declare(strict_types=1);

namespace AxleusIntegrationTest\ConfigManager;

use Axleus\ConfigManager\ConfigManager;
use Axleus\ConfigManager\Event\ConfigEvent;
use AxleusTestResource\ConfigManager as Resource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

use function file_exists;
use function file_put_contents;
use function is_dir;
use function is_file;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;
use function var_export;

#[CoversClass(ConfigManager::class)]
#[UsesClass(ConfigEvent::class)]
final class ConfigManagerTest extends TestCase
{
    private const CACHE_FILE     = '/config-cache.php';
    private const UPDATED_CONFIG = [
        Resource\FooConfigProvider::class => [
            'baz'     => 'yada',
            'key_old' => 'value_new',
        ],
    ];
    private string $dir;
    private string $cacheFile;
    private string $targetFile;

    private array $config = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/axleus_config_manager';
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }
        $this->cacheFile  = $this->dir . self::CACHE_FILE;
        $this->targetFile = $this->dir;
        $this->config     = (new Resource\FooConfigProvider())();
        file_put_contents($this->cacheFile, var_export($this->config, true));
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
        $this->config['debug'] = false;
        $configManager         = new ConfigManager($this->config);
        $event                 = new ConfigEvent();
        $event->setTargetCache($this->cacheFile);
        $eventResult = $configManager->onBustCache($event);
        self::assertFileDoesNotExist($this->cacheFile);
        self::assertTrue($eventResult === true);
    }

    public function testConfigManagerCanWriteUpdatedConfig(): void
    {
        $this->config['debug'] = true;
        $configManager         = new ConfigManager($this->config);
        $configEvent           = new ConfigEvent(null, Resource\FooConfigProvider::class);
        $this->targetFile     .= '/' . Resource\FooConfigProvider::TARGET_FILE;
        $configEvent->setTargetFile($this->targetFile);
        $configEvent->setUpdatedConfig(self::UPDATED_CONFIG);
        $eventResult = $configManager->onSaveConfig($configEvent);
        self::assertFileExists($configEvent->getTargetFile());
        $writtenData = include $this->targetFile;
        self::assertSame(self::UPDATED_CONFIG, $writtenData);
        self::assertTrue($eventResult === true);
    }

    public function testConfigManagerStopsPropagationWhenInDevelopmentMode(): void
    {
        $this->config['debug'] = true;
        $configManager         = new ConfigManager($this->config);
        $configEvent           = new ConfigEvent(null, Resource\FooConfigProvider::class);
        $eventResult           = $configManager->onSaveConfig($configEvent);
        self::assertTrue($configEvent->propagationIsStopped() === true);
    }
}
