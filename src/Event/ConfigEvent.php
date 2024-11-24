<?php

declare(strict_types=1);

namespace Axleus\ConfigManager\Event;

use Laminas\EventManager\Event;

class ConfigEvent extends Event
{
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

    public function setFilename(string $filename): self
    {
        $this->setParam('filename', $filename);
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->getParam('filename');
    }
}
