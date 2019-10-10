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
            $this->cards[$card->type()] = $card;
        }
    }

    // @todo remove in favour of... what? (match, player, realm, position)? :S
    public function ofType(CardId $card): Card
    {
        return $this->cards[$card->id()];
    }
}
