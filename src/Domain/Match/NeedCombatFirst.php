<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NeedCombatFirst extends RuntimeException
{
    public static function cannotJustSwitchPhase(): self
    {
        return new self('Need combat first.');
    }
}
