<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

final class EndCardPlaying
{
    /** @var int */
    private $player;
    /** @var MatchId */
    private $match;

    private function __construct(int $player, MatchId $match)
    {
        $this->player = $player;
        $this->match = $match;
    }

    public static function phase(int $player, MatchId $match): self
    {
        return new self($player, $match);
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
