<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_filter;
use Stratadox\CardGame\Match\MatchId;

class Battlefield
{
    private $cards = [];

    public function add(Card $card, MatchId $match): void
    {
        $this->cards[$match->id()][] = $card;
    }

    public function remove(Card $cardToRemove, MatchId $match): void
    {
        $this->cards[$match->id()] = array_filter(
            $this->cards[$match->id()],
            function (Card $card) use ($cardToRemove): bool {
                return !$card->is($cardToRemove);
            }
        );
    }

    /** @return Card[] */
    public function cardsInPlay(MatchId $match): array
    {
        return $this->cards[$match->id()] ?? [];
    }

    /** @return Card[] */
    public function attackers(MatchId $match): array
    {
        return array_filter(
            $this->cardsInPlay($match),
            function (Card $card): bool {
                return $card->isAttacking();
            }
        );
    }
}
