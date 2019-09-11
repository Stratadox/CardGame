<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface MatchIdGenerator
{
    public function generate(): MatchId;
}
