<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Player\PlayerId;

interface Cards
{
    public function add(Card $card): void;
    /** @return Card[] */
    public function inHandOf(PlayerId $thePlayer): array;
    public function topMostCardIn(DeckId $theDeck): Card;
    public function withId(CardId $id): Card;
}
