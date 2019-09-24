<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

final class AddedTheCardToTheMatch implements DeckEvent
{
    private $deck;
    private $cardNumber;

    public function __construct(DeckId $deck, int $cardNumber)
    {
        $this->deck = $deck;
        $this->cardNumber = $cardNumber;
    }

    public function aggregateId(): DeckId
    {
        return $this->deck;
    }

    public function deck(): DeckId
    {
        return $this->aggregateId();
    }

    public function cardNumber(): int
    {
        return $this->cardNumber;
    }

    public function payload(): array
    {
        return [/*@todo*/];
    }
}
