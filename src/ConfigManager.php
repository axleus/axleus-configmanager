<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

use Laminas\Config\Factory;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

final class ConfigManager extends AbstractListenerAggregate
{
    public function __construct(
        private array $config
    ) {
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_BUST_CACHE,
            [$this, 'onBustCache', $priority]
        );
    }

    public function onBustCache(Event\ConfigEvent $event)
    {
        // handle busting the cache after save
    }

    public function onLoadConfig(Event\ConfigEvent $event)
    {
        // handle loading config
    }

    public function onSaveConfig(Event\ConfigEvent $event)
    {
        // handle saving config ie writing the file.
    }
}
