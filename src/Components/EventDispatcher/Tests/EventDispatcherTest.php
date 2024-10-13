<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher\Tests;

use Kaizen\Components\EventDispatcher\Event;
use Kaizen\Components\EventDispatcher\EventDispatcher;
use Kaizen\Components\EventDispatcher\Tests\Fixtures\TestCustomEvent;
use Kaizen\Components\EventDispatcher\Tests\Fixtures\TestEventSubscriber;
use Kaizen\Components\EventDispatcher\Tests\Fixtures\TestEventSubscriberWithTypo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(EventDispatcher::class)]
class EventDispatcherTest extends TestCase
{
    public function testDispatchEvent(): void
    {
        $testCustomEvent = new TestCustomEvent();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerListener(
            $testCustomEvent::class,
            function (TestCustomEvent $testCustomEvent): void {$testCustomEvent->isCalled = 10; }
        );
        $eventDispatcher->dispatch($testCustomEvent);

        $this->assertTrue($eventDispatcher->hasListeners($testCustomEvent::class));
        $this->assertSame(10, $testCustomEvent->isCalled);
    }

    public function testDispatchNamedEvent(): void
    {
        $event = new class extends Event {
            public const string NAME = 'event.name';

            public bool $isCalled = false;
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerListener('event.name', function ($event): void {$event->isCalled = true; });

        $this->assertTrue($eventDispatcher->hasListeners('event.name'));
        $eventDispatcher->dispatch($event);

        $this->assertTrue($event->isCalled);
    }

    public function testDispatchEventOrder(): void
    {
        $event = new class extends Event {
            public bool $isCalledOnce = false;

            public bool $isCalledTwice = false;
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerListener($event::class, function ($event): void {$event->isCalledOnce = true; });
        $eventDispatcher->registerListener($event::class, function ($event): void {$event->isCalledTwice = true; });

        $eventDispatcher->dispatch($event);

        $this->assertTrue($event->isCalledOnce);
        $this->assertTrue($event->isCalledTwice);
    }

    public function testRegisterSubscriber(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerSubscriber(new TestEventSubscriber());

        $testCustomEvent = new TestCustomEvent();
        $this->assertSame(0, $testCustomEvent->isCalled);
        $listeners = $eventDispatcher->getListeners($testCustomEvent::class);

        $this->assertCount(1, $listeners);
        $listener = current($listeners);
        $this->assertIsCallable($listener);

        $listener($testCustomEvent);

        $this->assertSame(10, $testCustomEvent->isCalled);
    }

    public function testGetNotRegisteredListener(): void
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerSubscriber(new TestEventSubscriber());

        $testCustomEvent = new TestCustomEvent();
        $this->assertSame(0, $testCustomEvent->isCalled);
        $listeners = $eventDispatcher->getListeners($testCustomEvent::class);

        foreach ($listeners as $callback) {
            $this->assertIsCallable($callback);
        }

        $this->assertCount(1, $listeners);

        $listener = current($listeners);
        $this->assertIsCallable($listener);

        $listener($testCustomEvent);

        $this->assertSame(10, $testCustomEvent->isCalled);
    }

    public function testDispatchNotRegisteredEvent(): void
    {
        $testCustomEvent = new TestCustomEvent();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->registerSubscriber(new TestEventSubscriber());
        $eventDispatcher->dispatch(new Event());

        $this->assertSame(0, $testCustomEvent->isCalled);
    }

    public function testDispatchEventWithMethodCallbackTypo(): void
    {
        $eventDispatcher = new EventDispatcher();

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(sprintf(
            'The method "foobat" does not exist in class "%s". Please ensure that the method specified in '.
            '"getSubscribedEvents()" for handling the "%s" event exists, is correctly named, and is public.',
            TestEventSubscriberWithTypo::class,
            TestCustomEvent::class
        ));

        $eventDispatcher->registerSubscriber(new TestEventSubscriberWithTypo());
    }
}
