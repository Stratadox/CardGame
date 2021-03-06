<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use BadMethodCallException;
use Stratadox\CardGame\Deck\CardId;
use Stratadox\CardGame\Match\Event\CardWasDrawn;
use Stratadox\CardGame\Match\Event\SpellVanishedToTheVoid;

final class SpellTemplate implements CardTemplate
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
        return [new SpellVanishedToTheVoid($match, $player, $offset)];
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
        return [];
    }

    public function defendingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [];
    }

    public function dyingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [];
    }

    public function regroupingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array {
        return [];
    }

    public function playingMove(int $position): Location
    {
        return Location::inVoid();
    }

    public function attackingMove(int $position): Location
    {
        throw new BadMethodCallException('Spells cannot attack.');
    }

    public function defendingMove(int $position): Location
    {
        throw new BadMethodCallException('Spells cannot defend.');
    }

    public function regroupingMove(int $position): Location
    {
        throw new BadMethodCallException('Spells cannot regroup.');
    }

    public function cost(): Mana
    {
        return $this->cost;
    }
}
