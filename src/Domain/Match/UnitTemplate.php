<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

// @todo remove reference to foreign context
use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\Event\CardWasDrawn;
use Stratadox\CardGame\Match\Event\UnitDied;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\Event\UnitMovedToAttack;
use Stratadox\CardGame\Match\Event\UnitMovedToDefend;
use Stratadox\CardGame\Match\Event\UnitRegrouped;

final class UnitTemplate implements CardTemplate
{
    private $card;
    private $cost;

    public function __construct(CardId $card, Mana $cost)
    {
        $this->card = $card;
        $this->cost = $cost;
    }

    public function playingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new UnitMovedIntoPlay($match, $this->card, $player, $offset)];
    }

    public function drawingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new CardWasDrawn($match, $this->card, $player, $offset)];
    }

    public function attackingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new UnitMovedToAttack($match, $player, $offset)];
    }

    public function defendingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new UnitMovedToDefend($match, $player, $offset)];
    }

    public function dyingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new UnitDied($match, $player, $offset)];
    }

    public function regroupingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [new UnitRegrouped($match, $player, $offset)];
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

    public function regroupingMove(int $position): Location
    {
        return Location::inPlay($position);
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
