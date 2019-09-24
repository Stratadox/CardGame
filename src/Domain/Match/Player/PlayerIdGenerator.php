<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\Match\Player\PlayerId;

interface PlayerIdGenerator
{
    public function generate(): PlayerId;
}
