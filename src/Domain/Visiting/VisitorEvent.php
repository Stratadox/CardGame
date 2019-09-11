<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\DomainEvent;

interface VisitorEvent extends DomainEvent
{
    public function aggregateId(): VisitorId;
}
