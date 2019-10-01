<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

final class CardWasDrawn implements MatchEvent
{
    private $match;
    private $player;
    private $card;

    public function __construct(MatchId $match, CardId $card, PlayerId $player)
    {
        $this->match = $match;
        $this->card = $card;
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

    public function card(): CardId
    {
        return $this->card;
    }

    public function payload(): array
    {
        return [];
    }
}
