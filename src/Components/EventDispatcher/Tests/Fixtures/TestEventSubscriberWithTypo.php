<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher\Tests\Fixtures;

use Kaizen\Components\EventDispatcher\EventSubscriberInterface;

class TestEventSubscriberWithTypo implements EventSubscriberInterface
{
    #[\Override]
    public function getSubscribedEvents(): array
    {
        return [
            TestCustomEvent::class => 'foobat',
        ];
    }

    public function foobar(TestCustomEvent $testCustomEvent): void {}
}
