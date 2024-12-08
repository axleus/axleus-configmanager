<?php

declare(strict_types=1);

namespace AxleusTest\ConfigManager;

use AxleusTestResource\ConfigManager\FooConfigProvider;
use PHPUnit\Framework\TestCase;

use function is_dir;
use function is_file;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;

final class ConfigWriterTest extends TestCase
{
    public final const CONFIG_FILE = __DIR__ . '/../config/test.global.php';

    private string $dir;
    protected string $targetFile = FooConfigProvider::CONFIG_MANAGER_TARGET_FILE;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . '/axleus_config_writer';
        if (! is_dir($this->dir)) {
            mkdir($this->dir);
        }

    }

    public function testConfigWriterCanWriteConfig(): void
    {

    }
}
