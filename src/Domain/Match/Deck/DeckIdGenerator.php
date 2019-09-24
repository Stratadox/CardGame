<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

interface DeckIdGenerator
{
    public function generate(): DeckId;
}
