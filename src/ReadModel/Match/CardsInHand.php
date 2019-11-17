<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_merge;
use function array_values;
use Stratadox\CardGame\Match\MatchId;

class CardsInHand
{
    /** @var Card[][][] */
    private $cards = [];

    public function draw(MatchId $match, int $player, Card ...$cards): void
    {
        $this->cards[$match->id()][$player] = array_merge(
            $this->ofPlayer($player, $match),
            $cards
        );
    }

    public function played(int $offset, MatchId $match, int $player): void
    {
        foreach ($this->cards[$match->id()][$player] as $cardNumber => $cardInHand) {
            if ($cardInHand->offset() === $offset) {
                unset($this->cards[$match->id()][$player][$cardNumber]);
            }
        }
        $this->cards[$match->id()][$player] = array_values(
            $this->cards[$match->id()][$player]
        );
    }

    /** @return Card[] */
    public function ofPlayer(int $player, MatchId $match): array
    {
        return $this->cards[$match->id()][$player] ?? [];
    }
}
