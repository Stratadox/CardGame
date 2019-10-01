<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class EndCardPlaying
{
    private $player;

    private function __construct(PlayerId $player)
    {
        $this->player = $player;
    }

    public static function phase(PlayerId $player): self
    {
        return new self($player);
    }

    public function player(): PlayerId
    {
        return $this->player;
    }
}
