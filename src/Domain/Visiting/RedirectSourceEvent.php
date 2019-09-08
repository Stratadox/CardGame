<?php declare(strict_types=1);

namespace Stratadox\CardGame\Visiting;

use Stratadox\CardGame\DomainEvent;

interface RedirectSourceEvent extends DomainEvent
{
    public function aggregateId(): string;
}
