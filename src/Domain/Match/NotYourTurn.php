<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use RuntimeException;

final class NotYourTurn extends RuntimeException
{
    public static function cannotPlayCards(): self
    {
        return new self('Cannot play cards right now');
    }

    public static function cannotEndCardPlayingPhase(): self
    {
        return new self('Cannot end the card playing phase right now');
    }

    public static function cannotDefend(): self
    {
        return new self('Cannot block at this time');
    }

    public static function cannotAttack(): self
    {
        return new self('Cannot attack at this time');
    }

    public static function cannotStartCombat(): self
    {
        return new self('Cannot start the combat at this time');
    }

    public static function cannotEndTurn(): self
    {
        return new self('Cannot end the turn at this time');
    }
}
