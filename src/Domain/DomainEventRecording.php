<?php declare(strict_types=1);

namespace Stratadox\CardGame;

trait DomainEventRecording
{
    protected $events = [];

    private function happened(DomainEvent ...$newEvents): void
    {
        foreach ($newEvents as $newEvent) {
            $this->events[] = $newEvent;
        }
    }

    public function domainEvents(): iterable
    {
        return $this->events;
    }

    public function eraseEvents(): void
    {
        $this->events = [];
    }
}
