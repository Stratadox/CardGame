<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\PlayerId;

interface DecidesWhoStarts
{
    public function chooseBetween(PlayerId ...$players): PlayerId;
}
