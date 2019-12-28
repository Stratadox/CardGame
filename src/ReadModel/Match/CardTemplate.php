<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

final class CardTemplate
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function type(): string
    {
        return $this->id;
    }

    public function is(CardTemplate $other): bool
    {
        return $this->id === $other->id;
    }
}
