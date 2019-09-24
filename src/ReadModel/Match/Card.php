<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use Stratadox\CardGame\Match\Card\CardId;

final class Card
{
    private $id;
    private $name;
    private $cost;

    public function __construct(CardId $id, string $name, int $cost)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
    }

    public function id(): CardId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function cost(): int
    {
        return $this->cost;
    }

    public function isTheSameAs(Card $theOtherCard): bool
    {
        return $this->name() === $theOtherCard->name();
    }
}
