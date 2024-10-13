<?php

declare(strict_types=1);

namespace Kaizen\Components\EventDispatcher\Tests\Fixtures;

use Kaizen\Components\EventDispatcher\Event;

class TestCustomEvent extends Event
{
    public int $isCalled = 0;
}
