<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use BadMethodCallException;
// @todo remove reference to foreign context
use Stratadox\CardGame\Deck\CardId;

final class SpellTemplate implements CardTemplate
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
        return [new SpellVanishedToTheVoid($match, $this->card, $player)];
    }

    public function drawingEvents(MatchId $match, int $player): array
    {
        return [new CardWasDrawn($match, $this->card, $player)];
    }

    public function attackingEvents(MatchId $match, int $player): array
    {
        return [];
    }

    public function defendingEvents(MatchId $match, int $player): array
    {
        return [];
    }

    public function dyingEvents(MatchId $match): array
    {
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

    public function cost(): Mana
    {
        return $this->cost;
    }
}
