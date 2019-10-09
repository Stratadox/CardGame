<?php declare(strict_types=1);

namespace Stratadox\CardGame;

interface RefusalEvent extends DomainEvent
{
    public function aggregateId(): CorrelationId;
}
