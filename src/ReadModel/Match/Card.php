<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

final class Card
{
    private $id;
    private $attacking = false;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function type(): string
    {
        return $this->id;
    }

    public function is(Card $theOther): bool
    {
        return $this->id === $theOther->id;
    }

    public function isAttacking(): bool
    {
        return $this->attacking;
    }

    public function attack(): void
    {
        $this->attacking = true;
    }
}
