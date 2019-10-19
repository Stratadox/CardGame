<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

// @todo remove reference to foreign context
use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\Event\CardWasDrawn;
use Stratadox\CardGame\Match\Event\UnitDied;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\Event\UnitMovedToAttack;

final class UnitTemplate implements CardTemplate
{
    private $card;
    private $cost;

    public function __construct(CardId $card, Mana $cost)
    {
        $this->card = $card;
        $this->cost = $cost;
    }

    public function playingEvents(MatchId $match, int $player): array
    {
        return [new UnitMovedIntoPlay($match, $this->card, $player)];
    }

    public function drawingEvents(MatchId $match, int $player): array
    {
        return [new CardWasDrawn($match, $this->card, $player)];
    }

    public function attackingEvents(MatchId $match, int $player): array
    {
        return [new UnitMovedToAttack($match, $this->card)];
    }

    public function defendingEvents(MatchId $match, int $player): array
    {
        // @todo add UnitMovedToDefend?
        return [];
    }

    public function dyingEvents(MatchId $match, int $player): array
    {
        return [new UnitDied($match, $this->card, $player)];
    }

    public function playingMove(int $position): Location
    {
        return Location::inPlay($position);
    }

    public function attackingMove(int $position): Location
    {
        return Location::attackingAt($position);
    }

    public function defendingMove(int $position): Location
    {
        return Location::defendingAgainst($position);
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
