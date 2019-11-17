<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Deck\CardId;

class CardTemplates
{
    /** @var CardTemplate[] */
    private $cards;

    public function __construct(CardTemplate ...$cards)
    {
        foreach ($cards as $card) {
            $this->cards[$card->type()] = $card;
        }
    }

    public function ofType(CardId $card): CardTemplate
    {
        return $this->cards[$card->id()];
    }
}
