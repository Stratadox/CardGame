<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Match\Match\Match;
use Stratadox\CardGame\Match\Match\Matches;
use Stratadox\CardGame\Match\Match\MatchId;
use Stratadox\CardGame\Match\Player\PlayerId;

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
