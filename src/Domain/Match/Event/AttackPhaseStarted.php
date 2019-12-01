<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class AttackPhaseStarted implements MatchEvent
{
    /** @var MatchId */
    private $match;

    public function __construct(MatchId $match)
    {
        $this->match = $match;
    }

    public function match(): MatchId
    {
        return $this->aggregateId();
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }
}
