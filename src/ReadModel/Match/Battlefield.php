<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

class Battlefield
{
    private $cards = [];

    public function add(Card $card): void
    {
        $this->cards[] = $card;
    }

    /** @return Card[] */
    public function cardsInPlay(): iterable
    {
        return $this->cards;
    }
}
