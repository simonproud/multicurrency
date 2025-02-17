<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

interface EventDispatcherInterface
{
    public function addListener(string $eventName, callable $listener): void;
    public function dispatch(string $eventName, $event = null): void;
}
