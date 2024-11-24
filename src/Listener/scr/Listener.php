<?php
declare(strict_types=1);

namespace Axleus\ConfigManager\Listener;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;

class Listener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach('GetConfigData', [$this, 'onGetConfigData'], $priority);
    }

    public function onGetConfigData(): void
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = require 'config/container.php';        
        $config = $container->get('Config');
    }
}