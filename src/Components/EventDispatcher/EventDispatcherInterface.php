<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher;

interface EventDispatcherInterface
{
    public function dispatch(Event $event, ?string $eventName = null): void;
}
