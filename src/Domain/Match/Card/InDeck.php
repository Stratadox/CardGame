<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Deck\DeckId;

final class InDeck extends Location
{
    private $deck;
    private $position;

    public function __construct(DeckId $deck, int $position)
    {
        $this->deck = $deck;
        $this->position = $position;
    }

    public function isInDeck(DeckId $whichDeck): bool
    {
        return $this->deck->is($whichDeck);
    }

    public function position(): int
    {
        return $this->position;
    }
}
