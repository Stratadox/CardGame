<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class TriedPlayingCardOutOfTurn implements MatchEvent
{
    private $match;
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

    public function match(): MatchId
    {
        return $this->aggregateId();
    }

    public function player(): int
    {
        return $this->player;
    }
}
