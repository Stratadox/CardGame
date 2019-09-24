<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

final class OkayLetsGo
{
    private $match;

    private function __construct(MatchId $match)
    {
        $this->match = $match;
    }

    public static function beginThat(MatchId $match): self
    {
        return new self($match);
    }

    public function match(): MatchId
    {
        return $this->match;
    }
}
