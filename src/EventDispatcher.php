<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(string $eventName, $event = null): void
    {
        if (!empty($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $listener) {
                $listener($event);
            }
        }
    }
}
