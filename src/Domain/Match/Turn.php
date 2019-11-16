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

    public function prohibitsPlaying(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            $this->phase->prohibitsPlaying() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function prohibitsAttacking(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            $this->phase->prohibitsAttacking() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 10;
    }

    public function prohibitsDefending(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            $this->phase->prohibitsDefending() ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function prohibitsStartingCombat(int $player, DateTimeInterface $when): bool
    {
        // @todo check if turn phase allows for starting combat
        return $this->currentPlayer !== $player ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    /** @throws NotYourTurn */
    public function endCardPlayingPhaseFor(int $player, DateTimeInterface $when): Turn
    {
        // @todo check if time ran out? (should we?)
        if ($this->currentPlayer !== $player) {
            throw NotYourTurn::cannotEndCardPlayingPhase();
        }
        // @todo make immutable
        $this->phase = $this->phase->endCardPlaying();
        $this->since = $when;
        return $this;
    }

    public function endCombatPhase(DateTimeInterface $when): Turn
    {
        // @todo make immutable
        $this->since = $when;
        $this->phase = $this->phase->endCombat();
        return $this;
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
