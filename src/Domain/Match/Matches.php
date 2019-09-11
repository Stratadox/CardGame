<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface Matches
{
    public function add(Match $match): void;
    public function playedBy(PlayerId $player): Match;
}
