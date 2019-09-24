<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

final class DeckHasBeenShuffled implements DeckEvent
{
    private $deck;

    public function __construct(DeckId $deck)
    {
        $this->deck = $deck;
    }

    public function aggregateId(): DeckId
    {
        return $this->deck;
    }

    public function deck(): DeckId
    {
        return $this->aggregateId();
    }

    public function payload(): array
    {
        return [];
    }
}
