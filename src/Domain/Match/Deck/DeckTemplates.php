<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\Account\AccountId;

interface DeckTemplates
{
    public function put(DeckTemplate $deckTemplate): void;
    public function findFor(AccountId $account): DeckTemplate;
}
