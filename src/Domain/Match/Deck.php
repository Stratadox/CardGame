<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_pop;
use function assert;

final class Deck
{
    private $id;
    private $cards;

    public function __construct(string $id, Card ...$cards)
    {
        $this->id = $id;
        $this->cards = $cards;
    }

    public function draw(): Card
    {
        // @todo throw if no more cards
        $card = array_pop($this->cards);
        assert($card instanceof Card);
        return $card;
    }
}
