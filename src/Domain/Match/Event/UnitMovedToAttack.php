<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\Match\MatchId;

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
