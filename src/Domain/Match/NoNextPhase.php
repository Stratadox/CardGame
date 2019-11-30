<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NoNextPhase extends RuntimeException
{
    public static function available(): self
    {
        return new self('No next phase for this turn.');
    }
}
