<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class Battlefield
{
    /** @var UnitCard[][] */
    private $units = [];

    public function addUnitFor(PlayerId $player, UnitCard $unit): void
    {
        // note to self: add less yagni..
        if (!isset($this->units[$player->id()])) {
            $this->units[$player->id()] = [];
        }
        $this->units[$player->id()][] = $unit;
    }
}
