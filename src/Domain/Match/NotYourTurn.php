<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NotYourTurn extends RuntimeException
{
    public static function cannotPlayCards(): self
    {
        return new self('Cannot play cards now, it is not your turn.');
    }

    public static function cannotDefend(): self
    {
        return new self('Cannot send cards to defend anymore.');
    }
}
