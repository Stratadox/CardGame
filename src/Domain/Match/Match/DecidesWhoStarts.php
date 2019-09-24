<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Match;

use Stratadox\CardGame\Match\Player\PlayerId;

interface DecidesWhoStarts
{
    public function chooseBetween(PlayerId ...$players): PlayerId;
}
