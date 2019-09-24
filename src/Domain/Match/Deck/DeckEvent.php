<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\DomainEvent;

interface DeckEvent extends DomainEvent
{
    public function aggregateId(): DeckId;
}
