<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NotYourTurn extends RuntimeException
{
    public static function cannotPlayCards(): self
    {
        return new self('Cannot play cards now, it is not your turn.');
    }
}
