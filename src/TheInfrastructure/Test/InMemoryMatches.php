<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function reset;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\Matches;
use Stratadox\CardGame\Match\PlayerId;

final class InMemoryMatches implements Matches
{
    /** @var Match[] */
    private $matches = [];

    public function add(Match $match): void
    {
        $this->matches[(string) $match->id()] = $match;
    }

    public function forPlayer(PlayerId $thePlayer): Match
    {
        return reset($this->matches); // @todo prove me wrong
//        foreach ($this->matches as $theMatch) {
//            if ($theMatch->isBeingPlayedBy($thePlayer)) {
//                return $theMatch;
//            }
//        }
    }
}
