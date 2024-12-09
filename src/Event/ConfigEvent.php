<?php

declare(strict_types=1);

namespace Axleus\ConfigManager\Event;

use Laminas\EventManager\Event;

class ConfigEvent extends Event
{
    public final const DEFAULT_CACHE     = __DIR__ . '/../../../../../data/cache/config-cache.php';
    public final const EVENT_CONFIG_SAVE = 'config.save';
    public final const EVENT_CONFIG_LOAD = 'config.load';
    public final const EVENT_BUST_CACHE  = 'config.bustcache';

    public function setPath(string $path): self
    {
        $this->setParam('path', $path);
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->getParam('path');
    }

    public function setTargetFile(string $targetFile): self
    {
        $this->setParam('targetFile', $targetFile);
        return $this;
    }

    public function getTargetFile(): ?string
    {
        return $this->getParam('targetFile');
    }

    public function setUpdatedConfig(array $updatedConfig): self
    {
        $this->setParam('updatedConfig', $updatedConfig);
        return $this;
    }

    public function getUpdatedConfig(): array
    {
        return $this->getParam('updatedConfig', []);
    }

    public function setTargetCache(string $path): self
    {
        $this->setParam('targetCache', $path);
        return $this;
    }

    public function getTargetCache(): string
    {
        return $this->getParam('targetCache', self::DEFAULT_CACHE);
    }

    public function testSaRun()
    {

    }
}