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
        // If save/write fails stop propagation so the cache will not be busted
        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_CONFIG_SAVE,
            [$this, 'onSaveConfig', $priority]
        );

        /**
         * Run this listener on all save events, but after the save happens.
         * Setting it up this way prevents us from having to test return types at call time and then trigger another
         * event from userland.
         */
        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_CONFIG_SAVE,
            [$this, 'onBustCache', 0]
        );

        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_BUST_CACHE,
            [$this, 'onBustCache', $priority]
        );

        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_CONFIG_LOAD,
            [$this, 'onLoadConfig', $priority]
        );
    }

    public function onBustCache(Event\ConfigEvent $event)
    {
        /**
         * locate config cache file in /data/cache
         * remove delete it
         */

        $configCacheFilePath = $this->config['config_cache_path'] ?? null;

        if (!$this->config['debug'] && file_exists($configCacheFilePath)) {
            $event->setFilename($configCacheFilePath);
            $configCacheFile = $event->getFilename();

            unlink($configCacheFile);
        }
    }

    public function onLoadConfig(Event\ConfigEvent $event)
    {
        $targetKey = $event->getTarget(); //This will hold the FQCN of the ConfigProvider for the config
        if (!empty($this->config['axleus_settings'][$targetKey])) {
            return $this->config['axleus_settings'][$targetKey];
        }
        // handle loading config
        return [];
    }

    public function onSaveConfig(Event\ConfigEvent $event)
    {        
        /**
         * merge config from $this->config with config passed
         * via event, allowing event provided data to overwrite
         * use Writer\PhpArray to write file via ->toFile()
         *
         * If ->toFile() fails then call ->stopPropagation() on event instance
         */

        $targetKey = $event->getTarget();
        if (!empty($targetKey)) {
            $this->config['axleus_settings'] = $targetKey;
        }

        $namespaceArray = explode("\\", $targetKey);
        $namespace = strtolower(implode("-", [$namespaceArray[0], $namespaceArray[1]]));

        try {
            $writer = new Writer\PhpArray();
            $config = $event->getFormData();
            $writer->toFile("config/autoload/$namespace.global.php", $config);
        } catch (\Exception $e) {
            $event->stopPropagation();
            throw $e;
        }
    }
}