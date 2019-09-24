<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

interface MatchIdGenerator
{
    public function generate(): MatchId;
}
