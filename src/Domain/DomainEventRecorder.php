<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface DomainEventRecorder
{
    /** @return DomainEvent[] */
    public function domainEvents(): iterable;
    public function eraseEvents(): void;
}
