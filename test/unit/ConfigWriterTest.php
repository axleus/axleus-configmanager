<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager;

use Axleus\ConfigManager\ConfigWriter;
use AxleusTestResource\ConfigManager\FooConfigProvider;
use Laminas\ConfigAggregator\ArrayProvider;
use PHPUnit\Framework\TestCase;

use function file_exists;
use function is_dir;
use function is_file;
use function mkdir;
use function realpath;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;

final class ConfigWriterTest extends TestCase
{
    public final const CONFIG_FILE = __DIR__ . '/../config/test.global.php';

    private string $dir;
    protected string $targetFile = FooConfigProvider::TARGET_FILE;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/axleus_config_writer';
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dir . '/' . FooConfigProvider::TARGET_FILE)) {
            @unlink($this->dir . '/' . FooConfigProvider::TARGET_FILE);
        }
        @rmdir($this->dir);
    }

    public function testConfigWriterCanWriteConfig(): void
    {
        $writer = new ConfigWriter([
            FooConfigProvider::class
        ]);
        $writer->writeConfig($this->dir . '/' . FooConfigProvider::TARGET_FILE);
        self::assertFileExists($this->dir . '/' . FooConfigProvider::TARGET_FILE);
    }
}
