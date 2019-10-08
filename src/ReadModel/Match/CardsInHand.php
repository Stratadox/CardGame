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

    // @todo use position over card template id!
    public function played(string $card, MatchId $match, int $player): void
    {
        foreach ($this->cards[$match->id()][$player] as $cardNumber => $cardInHand) {
            if ($card === $cardInHand->id()) {
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
