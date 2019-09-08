<?php declare(strict_types=1);

namespace Stratadox\CardGame;

trait DomainEventRecording
{
    private $events = [];

    public function domainEvents(): iterable
    {
        return $this->events;
    }

    public function eraseEvents(): void
    {
        $this->events = [];
    }
}
