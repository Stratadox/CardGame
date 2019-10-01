<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

final class UnitMovedIntoPlay implements MatchEvent
{
    private $card;
    private $match;
    private $player;

    public function __construct(
        MatchId $match,
        CardId $card,
        PlayerId $player
    ) {
        $this->card = $card;
        $this->match = $match;
        $this->player = $player;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
