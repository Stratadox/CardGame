<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class EndTheTurn
{
    private $player;

    public function __construct(PlayerId $player)
    {
        $this->player = $player;
    }

    public static function for(PlayerId $player): self
    {
        return new self($player);
    }

    public function player(): PlayerId
    {
        return $this->player;
    }
}
