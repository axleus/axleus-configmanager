<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use SplFileInfo;
use Webimpress\SafeWriter\Exception\ExceptionInterface as FileWriterException;

use function getcwd;

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
            [$this, 'onSaveConfig'],
            $priority
        );

        /**
         * Run this listener on all save events, but after the save happens.
         * Setting it up this way prevents us from having to test return types at call time and then trigger another
         * event from userland.
         */
        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_CONFIG_SAVE,
            [$this, 'onBustCache'],
            0
        );

        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_BUST_CACHE,
            [$this, 'onBustCache'],
            $priority
        );

        $this->listeners[] = $events->attach(
            Event\ConfigEvent::EVENT_CONFIG_LOAD,
            [$this, 'onLoadConfig'],
            $priority
        );
    }

    public function onBustCache(Event\ConfigEvent $event): bool
    {
        // if this is development-mode then skip out now
        if ($this->config['debug']) {
            return true;
        }
        $fileInfo = new SplFileInfo(getcwd() . '/' . $this->config['config_cache_path']);
        if (! $fileInfo->isDir() && $fileInfo->isFile() && $fileInfo->isWritable()) {
            $path = $fileInfo->getRealPath();
            unset($fileInfo);
            return unlink($path);
        }
        return false;
    }

    public function onLoadConfig(Event\ConfigEvent $event): array
    {
        return $this->config[$event->getTarget()] ?? [];
    }

    public function onSaveConfig(Event\ConfigEvent $event)
    {
        try {
            $targetProvider = $event->getTarget();
            $targetFile     = getcwd() . '/config/autoload/' . $event->getTargetFile();
            $targetFilePath = (new SplFileInfo($targetFile))->getRealPath();
            $currentConfig  = [$targetProvider => $this->config[$targetProvider]];
            // read, merge and process the config, no caching during this write
            $configWriter   = new ConfigWriter([
                new ArrayProvider($currentConfig),
                new ArrayProvider([$targetProvider => $event->getUpdatedConfig()])
            ]);
            // write file
            $configWriter->writeConfig($targetFilePath);
        } catch (FileWriterException $e) {
            $event->stopPropagation();
            throw $e;
        }
        if ($this->config['debug']) {
            $event->stopPropagation();
        }
        return true;
    }
}