<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\PlayerId;

final class InMemoryMatches implements Matches
{
    /** @var Match[] */
    private $matches = [];

    public function add(Match $match): void
    {
        $this->matches[] = $match;
    }

    public function playedBy(PlayerId $player): Match
    {
        foreach ($this->matches as $match) {
            if ($match->isBeingPlayedBy($player)) {
                return $match;
            }
        }
    }
}
