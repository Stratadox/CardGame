<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class Turn
{
    private $currentPlayer;

    public function __construct(PlayerId $player)
    {
        $this->currentPlayer = $player;
    }

    public function allowsPlaying(Card $theCard): bool
    {
        return $this->currentPlayer->is($theCard->owner());
    }

    public function prohibitsPlaying(Card $theCard): bool
    {
        return !$this->allowsPlaying($theCard);
    }
}
