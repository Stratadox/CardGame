<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;

final class Turn
{
    private $currentPlayer;
    private $since;
    private $phase;

    public function __construct(
        int $player,
        DateTimeInterface $since,
        TurnPhase $phase = null
    ) {
        $this->currentPlayer = $player;
        $this->since = $since;
        $this->phase = $phase ?: TurnPhase::play();
    }

    /** @throws NotYourTurn */
    public function mustAllowCardPlaying(
        int $player,
        DateTimeInterface $when
    ): void {
        if (
            $this->currentPlayer !== $player ||
            $this->phase->prohibitsPlaying() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20
        ) {
            throw NotYourTurn::cannotPlayCards();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowAttacking(
        int $player,
        DateTimeInterface $when
    ): void {
        if (
            $this->currentPlayer !== $player ||
            $this->phase->prohibitsAttacking() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 10
        ) {
            throw NotYourTurn::cannotAttack();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowDefending(
        int $player,
        DateTimeInterface $when
    ): void {
        if (
            $this->currentPlayer !== $player ||
            $this->phase->prohibitsDefending() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20
        ) {
            throw NotYourTurn::cannotDefend();
        }
    }

    /** @throws NotYourTurn */
    public function mustAllowStartingCombat(
        int $player,
        DateTimeInterface $when
    ): void {
        // @todo check if turn phase allows for starting combat
        if (
            $this->currentPlayer !== $player ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20
        ) {
            throw NotYourTurn::cannotStartCombat();
        }
    }

    /** @throws NotYourTurn */
    public function endCardPlayingPhaseFor(
        int $player,
        DateTimeInterface $when
    ): Turn {
        // @todo check if time ran out? (should we?)
        if ($this->currentPlayer !== $player) {
            throw NotYourTurn::cannotEndCardPlayingPhase();
        }
        return new Turn(
            $this->currentPlayer,
            $when,
            $this->phase->endCardPlaying()
        );
    }

    public function endCombatPhase(DateTimeInterface $when): Turn
    {
        return new Turn(
            $this->currentPlayer,
            $when,
            $this->phase->endCombat()
        );
    }

    public function hasNotHadCombatYet(): bool
    {
        return !$this->phase->isAfterCombat();
    }

    /** @throws NotYourTurn */
    public function beginTheTurnOf(
        int $player,
        DateTimeInterface $since,
        int $previousPlayer,
        bool $shouldDefendFirst
    ): Turn {
        if (
            $this->currentPlayer !== $previousPlayer ||
            $since->getTimestamp() - $this->since->getTimestamp() >= 10
        ) {
            throw NotYourTurn::cannotEndTurn();
        }
        return new Turn(
            $player,
            $since,
            $shouldDefendFirst ? TurnPhase::defend() : TurnPhase::play()
        );
    }
}
