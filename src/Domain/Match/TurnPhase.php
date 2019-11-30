<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;

final class TurnPhase
{
    private const DEFEND = 0;
    private const PLAY = 1;
    private const ATTACK = 2;
    private const ALLOWED_TIME_FOR = [
        self::DEFEND => 20,
        self::PLAY => 20,
        self::ATTACK => 10,
    ];

    /** @var int */
    private $phase;
    /** @var DateTimeInterface */
    private $since;

    private function __construct(int $phase, DateTimeInterface $since)
    {
        $this->phase = $phase;
        $this->since = $since;
    }

    public static function play(DateTimeInterface $now): self
    {
        return new self(TurnPhase::PLAY, $now);
    }

    public static function defendOrPlay(
        bool $shouldWeDefend,
        DateTimeInterface $since
    ): self {
        return new self($shouldWeDefend ? self::DEFEND : self::PLAY, $since);
    }


    public function prohibitsDefending(DateTimeInterface $now): bool
    {
        return $this->phase !== TurnPhase::DEFEND || $this->hasExpired($now);
    }

    public function prohibitsPlaying(DateTimeInterface $now): bool
    {
        return $this->phase !== TurnPhase::PLAY || $this->hasExpired($now);
    }

    public function prohibitsAttacking(DateTimeInterface $now): bool
    {
        return $this->phase !== TurnPhase::ATTACK || $this->hasExpired($now);
    }

    public function isAfterCombat(): bool
    {
        return $this->phase === TurnPhase::PLAY
            || $this->phase === TurnPhase::ATTACK;
    }

    public function hasExpired(DateTimeInterface $now): bool
    {
        return $this->isTooFarApart(
            $now->getTimestamp(),
            $this->since->getTimestamp()
        );
    }

    public function endCombat(DateTimeInterface $now): TurnPhase
    {
        return new self(TurnPhase::PLAY, $now);
    }

    public function endCardPlaying(DateTimeInterface $now): TurnPhase
    {
        return new self(TurnPhase::ATTACK, $now);
    }

    /** @throws NoNextPhase */
    public function next(DateTimeInterface $now): TurnPhase
    {
        switch ($this->phase) {
//            @todo case self::DEFEND
            case self::PLAY: return $this->endCardPlaying($now);
            default: throw NoNextPhase::available();
        }
    }

    private function isTooFarApart(int $nowTime, int $backThenTime): bool
    {
        return $nowTime - $backThenTime >= self::ALLOWED_TIME_FOR[$this->phase];
    }
}
