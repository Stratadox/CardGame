<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface DecidesWhoStarts
{
    public function chooseBetween(PlayerId ...$players): PlayerId;
}
