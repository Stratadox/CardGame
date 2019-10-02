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

    // @todo is this out id or our template?
    public function id(): string
    {
        return $this->id;
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
