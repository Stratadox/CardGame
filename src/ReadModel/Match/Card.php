<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

// @todo split into Card and CardTemplate
final class Card
{
    private $id;
    private $attacking;

    public function __construct(string $id, bool $attacking = false)
    {
        $this->id = $id;
        $this->attacking = $attacking;
    }

    public function type(): string
    {
        return $this->id;
    }

    public function is(Card $that): bool
    {
        return $this->id === $that->id
            && $this->attacking === $that->attacking;
    }

    public function isAttacking(): bool
    {
        return $this->attacking;
    }

    public function attack(): void
    {
        $this->attacking = true;
    }

    public function regroup(): void
    {
        $this->attacking = false;
    }
}
