<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class TurnPhase
{
    private const DEFEND = 0;
    private const PLAY = 1;
    private const ATTACK = 2;

    private $phase;

    private function __construct(int $phase)
    {
        $this->phase = $phase;
    }

    public static function defend(): self
    {
        return new self(TurnPhase::DEFEND);
    }

    public static function play(): self
    {
        return new self(TurnPhase::PLAY);
    }

    public static function attack(): self
    {
        return new self(TurnPhase::ATTACK);
    }

    public function prohibitsDefending(): bool
    {
        return $this->phase !== TurnPhase::DEFEND;
    }

    public function prohibitsPlaying(): bool
    {
        return $this->phase !== TurnPhase::PLAY;
    }

    public function prohibitsAttacking(): bool
    {
        return $this->phase !== TurnPhase::ATTACK;
    }

    public function isAfterCombat(): bool
    {
        return $this->phase === TurnPhase::PLAY
            || $this->phase === TurnPhase::ATTACK;
    }

    public function endCombat(): TurnPhase
    {
        return TurnPhase::play();
    }

    public function endCardPlaying(): TurnPhase
    {
        return TurnPhase::attack();
    }
}
