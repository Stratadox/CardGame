<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

final class NextTurnBegan implements MatchEvent
{
    /** @var MatchId */
    private $match;
    /** @var int */
    private $player;

    public function __construct(MatchId $match, int $player)
    {
        $this->match = $match;
        $this->player = $player;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function player(): int
    {
        return $this->player;
    }

    public function match(): MatchId
    {
        return $this->aggregateId();
    }
}
