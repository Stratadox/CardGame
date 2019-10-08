<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use DateTimeInterface;

final class Turn
{
    private $currentPlayer;
    private $since;
    private $canPlay = true;

    public function __construct(int $player, DateTimeInterface $since)
    {
        $this->currentPlayer = $player;
        $this->since = $since;
    }

    public function allowsPlaying(Card $theCard, DateTimeInterface $when): bool
    {
        return $this->currentPlayer === $theCard->owner() &&
            $this->canPlay &&
            $this->isInTime($when->getTimestamp() - $this->since->getTimestamp());
    }

    public function prohibitsPlaying(Card $theCard, DateTimeInterface $when): bool
    {
        return !$this->allowsPlaying($theCard, $when);
    }

    public function endCardPlayingPhaseFor(int $thePlayer): Turn
    {
        $this->canPlay = false;
        return $this;
    }

    private function isInTime(int $interval): bool
    {
        return $interval < 20;
    }

    public function of(int $thePlayer, DateTimeInterface $since): Turn
    {
        return new Turn($thePlayer, $since);
    }
}
