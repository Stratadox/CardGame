<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

final class UnitMovedToAttack implements MatchEvent
{
    private $card;
    private $match;

    public function __construct(
        MatchId $match,
        CardId $card
    ) {
        $this->card = $card;
        $this->match = $match;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function card(): CardId
    {
        return $this->card;
    }
}
