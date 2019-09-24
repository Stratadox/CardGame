<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Card;

use Stratadox\CardGame\Match\Player\PlayerId;

final class InHand extends Location
{
    private $player;
    private $position;

    public function __construct(PlayerId $player, int $position)
    {
        $this->player = $player;
        $this->position = $position;
    }

    public function isInHandOf(PlayerId $whichPlayer): bool
    {
        return $this->player->is($whichPlayer);
    }

    public function position(): ?int
    {
        return $this->position;
    }

    public function toHand(PlayerId $ofPlayer, int $position): Location
    {
        return $this; // @todo or throw?
    }
}
