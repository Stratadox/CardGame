<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NotEnoughMana extends RuntimeException
{
    public static function toPlayThatCard(): self
    {
        return new self('Not enough mana!');
    }
}
