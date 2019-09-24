<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\Match\MatchId;
use Stratadox\CardGame\Match\Player\PlayerId;

final class OngoingMatch
{
    private $id;
    private $players;
    private $turn;

    public function __construct(MatchId $id, PlayerId $whoStarts, PlayerId ...$players)
    {
        $this->id = $id;
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
