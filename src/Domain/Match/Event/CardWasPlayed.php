<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\CardId;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\MatchId;
use Stratadox\CardGame\PlayerId;

final class CardWasPlayed implements MatchEvent
{
    private $match;
    private $player;
    private $card;

    public function __construct(MatchId $match, PlayerId $player, CardId $card)
    {
        $this->match = $match;
        $this->player = $player;
        $this->card = $card;
    }

    public function aggregateId(): MatchId
    {
        return $this->match;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    public function card(): CardId
    {
        return $this->card;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
