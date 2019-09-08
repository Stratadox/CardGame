<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\PlayerId;

final class PlayTheCard
{
    private $offset;
    private $player;

    private function __construct(int $offset, PlayerId $player)
    {
        $this->offset = $offset;
        $this->player = $player;
    }

    public static function number(int $offset, PlayerId $player): self
    {
        return new self($offset, $player);
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function player(): PlayerId
    {
        return $this->player;
    }
}
