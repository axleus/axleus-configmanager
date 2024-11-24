<?php
declare(strict_types=1);

namespace Axleus\ConfigManager\Event;

use Laminas\EventManager\EventManager;

class Event
{
    public function __construct(
        private EventManager $eventManager
    ) {
    }

    public function run()
    {
        $this->eventManager->trigger('GetConfigData', $this);
    }
}