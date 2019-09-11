<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function array_reverse;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\CardId;
use Stratadox\CardGame\Match\Deck;
use Stratadox\CardGame\Match\Decks;
use Stratadox\CardGame\Match\Mana;
use Stratadox\CardGame\Match\SpellCard;
use Stratadox\CardGame\Match\UnitCard;
use Stratadox\CardGame\Match\PlayerId;

final class InMemoryDecks implements Decks
{
    public function for(AccountId $account, PlayerId $player): Deck
    {
        return new Deck("deck for $player", ...array_reverse([
            new UnitCard(CardId::from('card-id-1'), $player, new Mana(2)),
            new UnitCard(CardId::from('card-id-2'), $player, new Mana(2)),
            new SpellCard(CardId::from('card-id-3'), $player, new Mana(4)),
            new UnitCard(CardId::from('card-id-4'), $player, new Mana(3)),
            new UnitCard(CardId::from('card-id-5'), $player, new Mana(5)),
            new UnitCard(CardId::from('card-id-6'), $player, new Mana(2)),
            new UnitCard(CardId::from('card-id-7'), $player, new Mana(2)),
            new UnitCard(CardId::from('card-id-8'), $player, new Mana(2)),
            new UnitCard(CardId::from('card-id-9'), $player, new Mana(2)),
        ]));
    }
}
