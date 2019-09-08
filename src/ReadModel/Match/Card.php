<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\CardId;

final class Card
{
    private $id;
    private $name;
    private $price;

    public function __construct(CardId $id, string $name, int $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function isTheSameAs(Card $theOtherCard): bool
    {
        return $this->name() === $theOtherCard->name();
    }
}
