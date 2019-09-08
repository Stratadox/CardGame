<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\Match\Deck;
use Stratadox\CardGame\Match\Decks;

final class InMemoryDecks implements Decks
{
    public function for(AccountId $player): Deck
    {
        return new Deck();
    }
}
