<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Account\AccountId;

interface Decks
{
    public function for(AccountId $account, PlayerId $player): Deck;
}
