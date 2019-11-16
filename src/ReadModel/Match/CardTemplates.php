<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Deck\CardId;

class CardTemplates
{
    /** @var Card[] */
    private $cards;

    public function __construct(Card ...$cards)
    {
        foreach ($cards as $card) {
            $this->cards[$card->type()] = $card;
        }
    }

    public function ofType(CardId $card): Card
    {
        return $this->cards[$card->id()];
    }
}
