<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface PlayerIdGenerator
{
    public function generate(): PlayerId;
}
