<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\MatchId;
use Stratadox\CardGame\Match\PlayerId;

final class OngoingMatch
{
    private $id;
    private $turn;
    private $players;

    public function __construct(MatchId $match, PlayerId $whoStarts, PlayerId ...$players)
    {
        $this->id = $match;
        $this->turn = $whoStarts;
        $this->players = $players;
    }

    public function id(): MatchId
    {
        return $this->id;
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
