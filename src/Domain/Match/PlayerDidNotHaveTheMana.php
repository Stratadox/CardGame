<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class PlayerDidNotHaveTheMana implements MatchEvent
{
    private $match;
    private $player;

    public function __construct(MatchId $match, PlayerId $player)
    {
        $this->match = $match;
        $this->player = $player;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }
}
