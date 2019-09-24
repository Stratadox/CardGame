<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

final class PrepareCard
{
    private $deck;
    private $cardOffset;

    private function __construct(DeckId $deck, int $cardOffset)
    {
        $this->deck = $deck;
        $this->cardOffset = $cardOffset;
    }

    public static function from(DeckId $deck): self
    {
        return new self($deck, 0);
    }

    public static function after(DeckId $deck, int $previous): self
    {
        return new self($deck, $previous + 1);
    }

    public function deck(): DeckId
    {
        return $this->deck;
    }

    public function cardOffset(): int
    {
        return $this->cardOffset;
    }
}
