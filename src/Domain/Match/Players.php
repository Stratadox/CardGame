<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_rand;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Players extends ImmutableCollection
{
    public function __construct(Player ...$players)
    {
        parent::__construct(...$players);
    }

    public function current(): Player
    {
        return parent::current();
    }

    public function pickRandom(): int
    {
        return (int) array_rand($this->items());
    }

    public function drawOpeningHands(MatchId $match): void
    {
        foreach ($this as $thePlayer) {
            $thePlayer->drawOpeningHand($match);
        }
    }

    public function after(int $player): int
    {
        return $player === 0 ? 1 : 0;
    }
}
