<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\MatchId;

final class OngoingMatch
{
    private $id;
    private $turn;

    public function __construct(MatchId $match, int $whoStarts)
    {
        $this->id = $match;
        $this->turn = $whoStarts;
    }

    public function id(): MatchId
    {
        return $this->id;
    }

    /** @return int[] */
    public function players(): array
    {
        return [0, 1];
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
