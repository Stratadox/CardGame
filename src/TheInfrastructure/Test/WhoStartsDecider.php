<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function array_rand;
use function assert;
use Stratadox\CardGame\Match\Match\DecidesWhoStarts;
use Stratadox\CardGame\Match\Player\PlayerId;

final class WhoStartsDecider implements DecidesWhoStarts
{
    public function chooseBetween(PlayerId ...$players): PlayerId
    {
        assert(!empty($players));
        return $players[array_rand($players)];
    }
}
