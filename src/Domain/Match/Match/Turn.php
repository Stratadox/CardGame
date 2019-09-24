<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use Stratadox\CardGame\Match\Player\PlayerId;

final class Turn
{
    private const PREPARATION = 0;

    private $status;

    private function __construct(int $status)
    {
        $this->status = $status;
    }

    public static function preparation(): self
    {
        return new self(self::PREPARATION);
    }

    public function hasNotStartedYet(): bool
    {
        return $this->status === self::PREPARATION;
    }

    public function beginTurnOf(PlayerId $player): Turn
    {
        return $this; // @todo
    }
}
