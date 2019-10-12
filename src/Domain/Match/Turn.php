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

    public function allowsPlaying(Card $theCard, DateTimeInterface $when): bool
    {
        return $this->currentPlayer === $theCard->owner() &&
            $this->canPlay &&
            $when->getTimestamp() - $this->since->getTimestamp() < 20;
    }

    public function prohibitsPlaying(Card $theCard, DateTimeInterface $when): bool
    {
        return !$this->allowsPlaying($theCard, $when);
    }

    public function prohibitsDefendingWith(Card $theCard, DateTimeInterface $when): bool
    {
        return $this->currentPlayer !== $theCard->owner() ||
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
