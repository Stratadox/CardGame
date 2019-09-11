<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NotYourTurn extends RuntimeException implements CannotPlayThisCard
{
    public static function cannotPlayCardsYet(): self
    {
        return new self('It is not your turn!');
    }
}
