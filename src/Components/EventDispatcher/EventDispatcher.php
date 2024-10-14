<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher;

use Kaizen\Components\EventDispatcher\Exception\ListenerNotFoundException;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string|class-string<Event>, callable[]>
     */
    private array $listeners = [];

    #[\Override]
    public function dispatch(Event $event, ?string $eventName = null): void
    {
        $eventName ??= '' === $event::NAME ? $event::class : $event::NAME;

        foreach ($this->listeners[$eventName] ?? [] as $listener) {
            $listener($event);
        }
    }

    /**
     * @param string|class-string<Event> $name
     */
    public function registerListener(string $name, callable $callback): void
    {
        $this->listeners[$name][] = $callback;
    }

    public function registerSubscriber(EventSubscriberInterface $eventSubscriber): void
    {
        foreach ($eventSubscriber->getSubscribedEvents() as $eventName => $eventCallbackName) {
            if (!method_exists($eventSubscriber, $eventCallbackName)) {
                throw new \RuntimeException(sprintf(
                    'The method "%s" does not exist in class "%s". Please ensure that the method specified in '.
                    '"getSubscribedEvents()" for handling the "%s" event exists, is correctly named, and is public.',
                    $eventCallbackName,
                    $eventSubscriber::class,
                    $eventName
                ));
            }

            $this->registerListener($eventName, $eventSubscriber->$eventCallbackName(...));
        }
    }

    /**
     * @param string|class-string<Event> $name
     */
    public function hasListeners(string $name): bool
    {
        return [] !== $this->listeners[$name];
    }

    /**
     * @param string|class-string<Event> $name
     *
     * @return callable[]
     * @throws ListenerNotFoundException
     */
    public function getListeners(string $name): array
    {
        if (false === $this->hasListeners($name)) {
            throw new ListenerNotFoundException(sprintf('Can not get listeners for "%s". Not found', $name));
        }

        return $this->listeners[$name];
    }
}
