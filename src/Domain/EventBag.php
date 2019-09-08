<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface EventBag
{
    public function add(DomainEvent ...$events): void;
    public function takeFrom(DomainEventRecorder $recorder): void;
    public function clear(): void;
    /** @return DomainEvent[] */
    public function all(): iterable;
}
