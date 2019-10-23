<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;

final class Turn
{
    private $currentPlayer;
    private $since;
    private $canPlay;
    private $canDefend;

    public function __construct(int $player, DateTimeInterface $since, bool $play = true)
    {
        $this->currentPlayer = $player;
        $this->since = $since;
        $this->canPlay = $play;
        $this->canDefend = !$play;
    }

    public function prohibitsPlaying(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            !$this->canPlay ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function prohibitsAttacking(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            $this->canPlay ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 10;
    }

    public function prohibitsDefending(int $player, DateTimeInterface $when): bool
    {
        // @todo check if turn phase allows for starting combat
        return $this->currentPlayer !== $player ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function prohibitsStartingCombat(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function prohibitsEndingCardPlaying(int $player, DateTimeInterface $when): bool
    {
        // @todo check if time ran out
        return $this->currentPlayer !== $player;
    }

    public function endCardPlayingPhaseFor(int $player): Turn
    {
        // @todo
        $this->canPlay = false;
        return $this;
    }

    public function endCombatPhase(): Turn
    {
        // @todo add time
        $this->canDefend = false;
        $this->canPlay = true;
        return $this;
    }

    /** @throws NotYourTurn */
    public function of(int $player, DateTimeInterface $since, int $previousPlayer): Turn
    {
        if (
            $this->currentPlayer !== $previousPlayer ||
            $since->getTimestamp() - $this->since->getTimestamp() >= 10
        ) {
            throw NotYourTurn::cannotEndTurn();
        }
        return new Turn($player, $since, false);
    }
}
