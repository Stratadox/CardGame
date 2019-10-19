<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\Deck;
use Stratadox\CardGame\Match\DeckForAccount;

final class InMemoryDecks implements DeckForAccount
{
    private $deckFor = [];

    public function deckFor(AccountId $account): Deck
    {
        return $this->deckFor[$account->id()] ?? new Deck();
    }
}
