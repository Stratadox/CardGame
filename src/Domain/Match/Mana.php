<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class Mana
{
    private $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function isLessThan(Mana $that): bool
    {
        return $this->amount < $that->amount;
    }

    public function minus(Mana $reduction): Mana
    {
        return new Mana($this->amount - $reduction->amount);
    }
}
