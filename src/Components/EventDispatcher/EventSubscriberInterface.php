<?php

namespace Kaizen\Components\EventDispatcher;

interface EventSubscriberInterface
{
    /**
     * This interface allow to register a set of custom events
     *
     * Example: return [ 'eventName' => 'methodName' ]
     *
     * @return array<string|class-string<Event>, string>
     */
    public function getSubscribedEvents(): array;
}
