<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher\Tests\Fixtures;

use Kaizen\Components\EventDispatcher\EventSubscriberInterface;

class TestEventSubscriber implements EventSubscriberInterface
{
    #[\Override]
    public function getSubscribedEvents(): array
    {
        return [
            TestCustomEvent::class => 'doSomething',
        ];
    }

    public function doSomething(TestCustomEvent $testCustomEvent): void
    {
        $testCustomEvent->isCalled = 10;
    }
}
