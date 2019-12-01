<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\AttackPhaseStarted;
use Stratadox\CardGame\Match\Event\DefendPhaseStarted;
use Stratadox\CardGame\Match\Event\PlayPhaseStarted;

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
    /** @var DomainEvent[] */
    private $events;

    private function __construct(
        int $phase,
        DateTimeInterface $since,
        DomainEvent ...$events
    ) {
        $this->phase = $phase;
        $this->since = $since;
        $this->events = $events;
    }

    public static function defendOrPlay(
        bool $shouldWeDefend,
        DateTimeInterface $now,
        MatchId $match
    ): self {
        return $shouldWeDefend ?
            self::defend($now, $match) :
            self::play($now, $match);
    }

    public static function play(DateTimeInterface $now, MatchId $match): self
    {
        return new self(TurnPhase::PLAY, $now, new PlayPhaseStarted($match));
    }

    private static function defend(DateTimeInterface $now, MatchId $match): self
    {
        return new self(TurnPhase::DEFEND, $now, new DefendPhaseStarted($match));
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

    public function prohibitsCombat(): bool
    {
        return $this->phase !== TurnPhase::DEFEND;
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

    public function startPlay(DateTimeInterface $now, MatchId $match): TurnPhase
    {
        return new self(TurnPhase::PLAY, $now, new PlayPhaseStarted($match));
    }

    public function startAttack(DateTimeInterface $now, MatchId $match): TurnPhase
    {
        return new self(TurnPhase::ATTACK, $now, new AttackPhaseStarted($match));
    }

    /** @throws NoNextPhase|NeedCombatFirst */
    public function next(DateTimeInterface $now, MatchId $match): TurnPhase
    {
        switch ($this->phase) {
            case self::DEFEND:  throw NeedCombatFirst::cannotJustSwitchPhase();
            case self::PLAY: return $this->startAttack($now, $match);
            default: throw NoNextPhase::available();
        }
    }

    public function events(): array
    {
        return $this->events;
    }

    private function isTooFarApart(int $nowTime, int $backThenTime): bool
    {
        return $nowTime - $backThenTime >= self::ALLOWED_TIME_FOR[$this->phase];
    }
}
