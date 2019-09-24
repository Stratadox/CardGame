<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use Stratadox\CardGame\DomainEvent;

interface MatchEvent extends DomainEvent
{
    public function aggregateId(): MatchId;
}
