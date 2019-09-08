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
            new Card(),
            new Card(),
            new Card(),
            new Card(),
            new Card(),
            new Card(),
            new Card(),
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
