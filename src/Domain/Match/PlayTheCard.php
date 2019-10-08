<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class PlayTheCard
{
    private $offset;
    private $player;
    private $match;

    private function __construct(int $offset, int $player, MatchId $match)
    {
        $this->offset = $offset;
        $this->player = $player;
        $this->match = $match;
    }

    public static function number(int $offset, int $player, MatchId $match): self
    {
        return new self($offset, $player, $match);
    }

    public function cardNumber(): int
    {
        return $this->offset;
    }

    public function player(): int
    {
        return $this->player;
    }

    public function match(): MatchId
    {
        return $this->match;
    }
}
