<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_merge;
use function assert;
use DateTimeInterface;
use Stratadox\CardGame\Match\Event\MatchStarted;
use Stratadox\CardGame\Match\Event\NextTurnStarted;

final class Turn
{
    /** @var int */
    private $currentPlayer;
    /** @var TurnPhase */
    private $phase;
    /** @var MatchEvent[] */
    private $events;

    private function __construct(
        int $currentPlayer,
        TurnPhase $phase,
        MatchEvent ...$events
    ) {
        $this->currentPlayer = $currentPlayer;
        $this->phase = $phase;
        $this->events = $events;
    }

    public static function first(
        int $player,
        DateTimeInterface $since,
        MatchId $match
    ): Turn {
        return new Turn(
            $player,
            TurnPhase::play($since, $match),
            new MatchStarted($match, $player)
        );
    }

    /** @throws NotYourTurn */
    public function mustAllowCardPlaying(
        int $player,
        DateTimeInterface $now
    ): void {
        if ($this->isNotOf($player) || $this->phase->prohibitsPlaying($now)) {
            throw NotYourTurn::cannotPlayCards();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowAttacking(
        int $player,
        DateTimeInterface $now
    ): void {
        if ($this->isNotOf($player) || $this->phase->prohibitsAttacking($now)) {
            throw NotYourTurn::cannotAttack();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowDefending(
        int $player,
        DateTimeInterface $now
    ): void {
        if ($this->isNotOf($player) || $this->phase->prohibitsDefending($now)) {
            throw NotYourTurn::cannotDefend();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowStartingCombat(int $player): void
    {
        if ($this->isNotOf($player) || $this->phase->prohibitsCombat()) {
            throw NotYourTurn::cannotStartCombat();
        }
    }

    public function events(): array
    {
        return array_merge($this->events, $this->phase->events());
    }

    public function hasNotHadCombatYet(): bool
    {
        return !$this->phase->isAfterCombat();
    }

    public function hasExpired(DateTimeInterface $now): bool
    {
        return $this->phase->hasExpired($now);
    }

    public function currentPlayer(): int
    {
        return $this->currentPlayer;
    }

    /** @throws NotYourTurn */
    public function endCardPlayingPhaseFor(
        int $player,
        DateTimeInterface $now,
        MatchId $match
    ): Turn {
        if ($this->isNotOf($player)) {
            throw NotYourTurn::cannotEndCardPlayingPhase();
        }
        return new Turn($this->currentPlayer, $this->phase->startAttack($now, $match));
    }

    public function startPlayPhase(DateTimeInterface $now, MatchId $match): Turn
    {
        return new Turn($this->currentPlayer, $this->phase->startPlay($now, $match));
    }

    /** @throws NotYourTurn */
    public function beginTheTurnOf(
        int $player,
        DateTimeInterface $now,
        int $previousPlayer,
        bool $shouldDefendFirst,
        MatchId $match
    ): Turn {
        if ($this->isNotOf($previousPlayer)) {
            throw NotYourTurn::cannotEndTurn();
        }
        return new Turn(
            $player,
            TurnPhase::defendOrPlay($shouldDefendFirst, $now, $match),
            new NextTurnStarted($match, $player)
        );
    }

    /** @throws NoNextPhase|NeedCombatFirst */
    public function endExpiredPhase(DateTimeInterface $now, MatchId $match): Turn
    {
        assert($this->hasExpired($now));

        return new self($this->currentPlayer, $this->phase->next($now, $match));
    }

    private function isNotOf(int $player): bool
    {
        return $this->currentPlayer !== $player;
    }
}
