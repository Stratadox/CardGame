<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_rand;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Players extends ImmutableCollection
{
    public function __construct(Player ...$players)
    {
        parent::__construct(...$players);
    }

    public function current(): Player
    {
        return parent::current();
    }

    public function pickRandomId(): PlayerId
    {
        return $this[array_rand($this->items())]->id();
    }

    public function withId(PlayerId $thePlayer): Player
    {
        foreach ($this as $thisOne) {
            if ($thePlayer->is($thisOne->id())) {
                return $thisOne;
            }
        }
    }

    public function drawOpeningHands(MatchId $match): void
    {
        foreach ($this as $thePlayer) {
            $thePlayer->drawOpeningHand($match);
        }
    }

    public function after(PlayerId $player): PlayerId
    {
        return $this->nextPlayer($player)->id();
    }

    private function nextPlayer(PlayerId $player): Player
    {
        // @todo better
        if ($player->is($this[0]->id())) {
            return $this[1];
        }
        return $this[0];
    }
}
