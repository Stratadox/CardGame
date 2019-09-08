<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\VisitorId;

interface VisitorEvent extends DomainEvent
{
    public function aggregateId(): VisitorId;
}
