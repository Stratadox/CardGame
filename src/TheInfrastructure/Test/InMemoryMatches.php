<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function array_values;
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
        // @todo throw if no such match
        return $this->matches[$match->id()];
    }

    public function ongoing(): array
    {
        // @todo filter this once matches can end
        return array_values($this->matches);
    }
}
