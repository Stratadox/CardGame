<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

final class UnitTemplate implements CardTemplate
{
    private $card;
    private $cost;

    public function __construct(CardId $card, Mana $cost)
    {
        $this->card = $card;
        $this->cost = $cost;
    }

    public function playingEvents(MatchId $match, PlayerId $player): array
    {
        return [new UnitMovedIntoPlay($match, $this->card, $player)];
    }

    public function drawingEvents(MatchId $match, PlayerId $player): array
    {
        return [new CardWasDrawn($match, $this->card, $player)];
    }

    public function playingMove(int $position): Location
    {
        return Location::inPlay($position);
    }

    public function cardIdentifier(): CardId
    {
        return $this->card;
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
