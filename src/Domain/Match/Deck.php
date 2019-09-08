<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_pop;

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
        return array_pop($this->cards);
    }
}
