<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\AccountId;

interface Decks
{
    public function for(AccountId $player): Deck;
}
