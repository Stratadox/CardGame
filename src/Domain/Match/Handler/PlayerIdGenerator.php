<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Handler;

use Stratadox\CardGame\PlayerId;

interface PlayerIdGenerator
{
    public function generate(): PlayerId;
}
