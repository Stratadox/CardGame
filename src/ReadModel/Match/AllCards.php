<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Deck\CardId;

class AllCards
{
    /** @var Card[] */
    private $cards;

    public function __construct(Card ...$cards)
    {
        foreach ($cards as $card) {
            $this->cards[$card->id()] = $card;
        }
    }

    // @todo how to handle doubles, if card id is a deck-cardId?
    public function withId(CardId $card): Card
    {
        return $this->cards[$card->id()];
    }
}
