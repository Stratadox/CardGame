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

    // @todo is this our id or our template?
    public function id(): string
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

    // @todo is this the match card or the deck card?
    public function attack(): void
    {
        $this->attacking = true;
    }
}
