<?php

declare(strict_types=1);

namespace Axleus\ConfigManager\Event;

use Laminas\EventManager\Event;

class ConfigEvent extends Event
{
    public final const EVENT_CONFIG_SAVE = 'config.save';
    public final const EVENT_CONFIG_LOAD = 'config.load';
    public final const EVENT_BUST_CACHE  = 'config.bustcache';
}
