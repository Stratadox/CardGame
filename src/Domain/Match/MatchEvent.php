<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\MatchId;

interface MatchEvent extends DomainEvent
{
    public function aggregateId(): MatchId;
}
