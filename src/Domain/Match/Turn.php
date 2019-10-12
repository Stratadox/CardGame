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
        return $this->currentPlayer !== $player;
    }

    public function prohibitsDefending(int $player, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $player ||
            !$this->canDefend ||
            $when->getTimestamp() - $this->since->getTimestamp() >= 20;
    }

    public function endCardPlayingPhaseFor(int $thePlayer): Turn
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

    public function of(int $thePlayer, DateTimeInterface $since): Turn
    {
        return new Turn($thePlayer, $since, false);
    }
}
