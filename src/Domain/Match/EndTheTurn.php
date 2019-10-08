<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class EndTheTurn
{
    private $match;
    private $player;

    public function __construct(MatchId $match, int $player)
    {
        $this->match = $match;
        $this->player = $player;
    }

    public static function for(MatchId $match, int $player): self
    {
        return new self($match, $player);
    }

    public function match(): MatchId
    {
        return $this->match;
    }

    public function player(): int
    {
        return $this->player;
    }
}
