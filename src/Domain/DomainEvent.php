<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface DomainEvent
{
    public function aggregateId();
    public function payload(): array;
}
