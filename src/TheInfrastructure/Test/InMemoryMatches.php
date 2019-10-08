<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\MatchId;

final class InMemoryMatches implements Matches
{
    /** @var Match[] */
    private $matches = [];

    public function add(Match $match): void
    {
        $this->matches[(string) $match->id()] = $match;
    }

    public function withId(MatchId $match): Match
    {
        return $this->matches[$match->id()];
    }
}
