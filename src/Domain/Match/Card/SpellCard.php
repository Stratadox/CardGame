<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

final class SpellCard extends Card
{
    public function play(): void
    {
        $this->location = $this->location->toVoid();

        $this->events[] = new SpellVanishedToTheVoid($this->id(), $this->template(), $this->owner());
    }
}
