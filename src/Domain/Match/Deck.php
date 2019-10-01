<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Deck\CardId;

final class Deck
{
    public function cardsFor(PlayerId $player): Cards
    {
        return new Cards(
            Card::inDeck($player, 7, new UnitTemplate(CardId::from('card-id-1'), new Mana(1))),
            Card::inDeck($player, 6, new UnitTemplate(CardId::from('card-id-2'), new Mana(3))),
            Card::inDeck($player, 5, new SpellTemplate(CardId::from('card-id-3'), new Mana(4))),
            Card::inDeck($player, 4, new UnitTemplate(CardId::from('card-id-4'), new Mana(6))),
            Card::inDeck($player, 3, new UnitTemplate(CardId::from('card-id-5'), new Mana(2))),
            Card::inDeck($player, 2, new UnitTemplate(CardId::from('card-id-6'), new Mana(5))),
            Card::inDeck($player, 1, new UnitTemplate(CardId::from('card-id-7'), new Mana(2))),
            Card::inDeck($player, 0, new UnitTemplate(CardId::from('card-id-8'), new Mana(2)))
        );
    }
}
