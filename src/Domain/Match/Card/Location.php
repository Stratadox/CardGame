<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Player\PlayerId;

abstract class Location
{
    public function isInDeck(DeckId $whichDeck): bool
    {
        return false;
    }

    public function isInHandOf(PlayerId $whichPlayer): bool
    {
        return false;
    }

    public function position(): ?int
    {
        return null;
    }

    public function toHand(PlayerId $ofPlayer, int $position): Location
    {
        return new InHand($ofPlayer, $position);
    }

    public function toPlay(): Location
    {
        return new InPlay();
    }

    public function toVoid(): Location
    {
        return new InVoid();
    }
}
