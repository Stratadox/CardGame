<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_merge as combine_cards;
use Stratadox\CardGame\PlayerId;

class CardsInHand
{
    private $cards;
    private $default;

    public function __construct()
    {
        // @todo this be cheating, make more tests
        $this->default = [
            new UnitCard('test 1', 2),
            new UnitCard('test 2', 4),
            new UnitCard('test 3', 3),
            new UnitCard('test 4', 1),
            new UnitCard('test 5', 2),
            new UnitCard('test 6', 5),
            new UnitCard('test 7', 2),
        ];
    }

    public function draw(PlayerId $player, Card ...$cards): void
    {
        $this->cards[$player->id()] = combine_cards($this->of($player), $cards);
    }

    /** @return Card[] */
    public function of(PlayerId $player): iterable
    {
        return $this->cards[$player->id()] ?? $this->default;
    }
}
