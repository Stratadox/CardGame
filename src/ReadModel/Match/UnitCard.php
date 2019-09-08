<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

final class UnitCard implements Card
{
    private $name;
    private $price;

    public function __construct(string $name, int $price)
    {
        $this->name = $name;
        $this->price = $price;
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
