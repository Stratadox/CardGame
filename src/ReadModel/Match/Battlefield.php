<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\MatchId;

class Battlefield
{
    private $cards = [];

    public function add(Card $card, MatchId $match): void
    {
        $this->cards[$match->id()][] = $card;
    }

    /** @return Card[] */
    public function cardsInPlay(MatchId $match): iterable
    {
        return $this->cards[$match->id()] ?? [];
    }
}
