<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function assert;
use DateTimeInterface;
use Stratadox\CardGame\Match\Event\MatchHasBegun;
use Stratadox\CardGame\Match\Event\NextTurnBegan;

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
        return new Turn($player, TurnPhase::play($since), new MatchHasBegun($match, $player));
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
        if ($this->isNotOf($player)) {
            throw NotYourTurn::cannotStartCombat();
        }
    }

    public function events(): array
    {
        return $this->events;
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
        DateTimeInterface $now
    ): Turn {
        // @todo assert current phase is card play phase
        if ($this->isNotOf($player)) {
            throw NotYourTurn::cannotEndCardPlayingPhase();
        }
        return new Turn($this->currentPlayer, $this->phase->endCardPlaying($now));
    }

    public function endCombatPhase(DateTimeInterface $now): Turn
    {
        return new Turn($this->currentPlayer, $this->phase->endCombat($now));
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
        return $this->nextTurn($player, $now, $shouldDefendFirst, $match);
    }

    /** @throws NoNextPhase */
    public function endExpiredPhase(DateTimeInterface $now): Turn
    {
        assert($this->hasExpired($now));

        return new self($this->currentPlayer, $this->phase->next($now));
    }

    private function isNotOf(int $player): bool
    {
        return $this->currentPlayer !== $player;
    }

    private function nextTurn(
        int $player,
        DateTimeInterface $now,
        bool $defend,
        MatchId $match
    ): Turn {
        return new Turn(
            $player,
            TurnPhase::defendOrPlay($defend, $now),
            new NextTurnBegan($match, $player)
        );
    }
}
