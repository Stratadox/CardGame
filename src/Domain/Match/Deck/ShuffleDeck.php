<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\Match\Player\PlayerId;

final class ShuffleDeck
{
    private $player;

    private function __construct(PlayerId $player)
    {
        $this->player = $player;
    }

    public static function for(PlayerId $player): self
    {
        return new self($player);
    }

    public function owner(): PlayerId
    {
        return $this->player;
    }
}
