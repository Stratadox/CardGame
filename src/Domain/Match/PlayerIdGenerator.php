<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\Match\PlayerId;

interface PlayerIdGenerator
{
    public function generate(): PlayerId;
}
