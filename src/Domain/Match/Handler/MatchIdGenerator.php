<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use Stratadox\CardGame\MatchId;

interface MatchIdGenerator
{
    public function generate(): MatchId;
}
