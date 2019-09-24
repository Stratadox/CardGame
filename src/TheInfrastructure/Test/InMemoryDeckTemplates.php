<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\Deck\DeckTemplate;
use Stratadox\CardGame\Match\Deck\DeckTemplates;

final class InMemoryDeckTemplates implements DeckTemplates
{
    /** @var DeckTemplate[] */
    private $templates;

    public function put(DeckTemplate $deckTemplate): void
    {
        $this->templates[(string) $deckTemplate->owner()] = $deckTemplate;
    }

    public function findFor(AccountId $account): DeckTemplate
    {
        return $this->templates[$account->id()];
    }
}
