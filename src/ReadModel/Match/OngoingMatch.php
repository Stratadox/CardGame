<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\PlayerId;

final class OngoingMatch
{
    private $players;
    private $turn;

    public function __construct(PlayerId $whoStarts, PlayerId ...$players)
    {
        $this->turn = $whoStarts;
        $this->players = $players;
    }

    /** @return PlayerId[] */
    public function players(): array
    {
        return $this->players;
    }

    public function itIsTheTurnOf(PlayerId $player): bool
    {
        return $this->turn->is($player);
    }
}
