<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

final class UnitCard extends Card
{
    public function play(): void
    {
        $this->location = $this->location->toPlay();

        $this->events[] = new UnitMovedIntoPlay($this->id(), $this->template(), $this->owner());
    }
}
