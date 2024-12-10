<?php

declare(strict_types=1);

namespace Axleus\ConfigManager;

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Webimpress\SafeWriter\Exception\ExceptionInterface as FileWriterException;

use function realpath;
use function unlink;

class ConfigManager extends AbstractListenerAggregate
{
    /**
     * @param non-empty-array{'config_cache_path': string, 'debug': bool} $config
     * @return void
     */
    public function __construct(
        private array $config
    ) {
    }

    /**
     * @param int $priority
     * @return void
     */
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
    }

    public function onBustCache(Event\ConfigEvent $event): bool
    {
        // if this is development-mode then skip out now
        if ($this->config['debug']) {
            return true;
        }

        return @unlink(realpath($event->getTargetCache()));
    }

    public function onSaveConfig(Event\ConfigEvent $event): bool
    {
        try {
            $isWritten      = false;
            $targetProvider = $event->getTarget();
            $targetFile     = $event->getTargetFile();
            // read, merge and process the config, no caching during this write
            $configWriter = new ConfigWriter([
                new ArrayProvider([$targetProvider => $this->config[$targetProvider]]),
                new ArrayProvider($event->getUpdatedConfig()),
            ]);
            if (! empty($targetFile)) {
                // write file
                $configWriter->writeConfig($targetFile);
                $isWritten = true;
            }
        } catch (FileWriterException $e) {
            $event->stopPropagation();
            throw $e;
        }
        if ($this->config['debug']) {
            $event->stopPropagation();
        }
        return $isWritten;
    }
}
