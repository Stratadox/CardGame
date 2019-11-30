<?php declare(strict_types=1);

namespace Stratadox\CardGame\Test;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Infrastructure\DomainEvents\Dispatcher;
use Stratadox\CardGame\Infrastructure\Test\TestClock;
use Stratadox\Clock\RewindableClock;
use Stratadox\CommandHandling\Handler;

interface Configuration
{
    public function commandHandler(
        EventBag $eventBag,
        RewindableClock $clock,
        Dispatcher $dispatcher
    ): Handler;

    public function configureClock(TestClock $clock, Handler $commandBus): void;
}
