<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

final class Card
{
    private $id;
    private $name;
    private $cost;

    public function __construct(string $id, string $name, int $cost)
    {
        $this->id = $id;
        $this->name = $name;
        $this->cost = $cost;
    }

    public function id(): string
    {
        return $this->id;
    }
}
