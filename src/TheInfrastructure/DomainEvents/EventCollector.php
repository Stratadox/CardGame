<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\DomainEvents;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\EventBag;

final class EventCollector implements EventBag
{
    private $events = [];

    public function add(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    public function takeFrom(DomainEventRecorder $recorder): void
    {
        $this->add(...$recorder->domainEvents());
        $recorder->eraseEvents();
    }

    public function clear(): void
    {
        $this->events = [];
    }

    public function all(): iterable
    {
        return $this->events;
    }
}
