<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\MatchId;

final class OngoingMatch
{
    private $id;
    private $turn;
    private $players;

    public function __construct(MatchId $match, int $whoStarts, int ...$players)
    {
        $this->id = $match;
        $this->turn = $whoStarts;
        $this->players = $players;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    /** @return int[] */
    public function players(): array
    {
        return $this->players;
    }

    public function beganTheTurnOf(int $player): void
    {
        $this->turn = $player;
    }

    public function itIsTheTurnOf(int $player): bool
    {
        return $this->turn === $player;
    }
}
