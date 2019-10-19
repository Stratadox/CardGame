<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Account\AccountId;

interface DeckForAccount
{
//    public function addFor(AccountId $account, Deck $deck): void;
    public function deckFor(AccountId $account): Deck;
}
