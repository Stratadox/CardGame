<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Deck\DeckId;

final class DrawCard
{
    private $deck;

    private function __construct(DeckId $deck)
    {
        $this->deck = $deck;
    }

    public static function from(DeckId $deck): self
    {
        return new self($deck);
    }

    public function deck(): DeckId
    {
        return $this->deck;
    }
}
