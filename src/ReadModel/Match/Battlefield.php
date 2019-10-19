<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_filter;
use function array_merge;
use Stratadox\CardGame\Match\MatchId;

class Battlefield
{
    private $cards = [];

    public function add(Card $card, MatchId $match, int $owner): void
    {
        $this->cards[$match->id()][$owner][] = $card;
    }

    public function remove(Card $cardToRemove, MatchId $match, int $owner): void
    {
        $this->cards[$match->id()][$owner] = array_filter(
            $this->cards[$match->id()][$owner],
            function (Card $card) use ($cardToRemove): bool {
                return !$card->is($cardToRemove);
            }
        );
    }

    /** @return Card[] */
    public function cardsInPlay(MatchId $match): array
    {
        return array_merge(...$this->cards[$match->id()] ?? [[]]);
    }

    /** @return Card[] */
    public function cardsInPlayFor(int $player, MatchId $match): array
    {
        return $this->cards[$match->id()][$player] ?? [];
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
