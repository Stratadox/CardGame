<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_filter;
use function array_merge;
use function current;
use Stratadox\CardGame\Match\MatchId;

class Battlefield
{
    private $cards = [];

    public function add(Card $card, MatchId $match, int $owner): void
    {
        $this->cards[$match->id()][$owner][] = $card;
    }

    public function remove(int $cardToRemove, MatchId $match, int $owner): void
    {
        $this->cards[$match->id()][$owner] = array_filter(
            $this->cards[$match->id()][$owner],
            static function (Card $card) use ($cardToRemove): bool {
                return $card->offset() !== $cardToRemove;
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
        return $this->cards[$match->id()][$player];
    }

    public function sendIntoBattle(
        int $offset,
        MatchId $match,
        int $player
    ): void {
        $this->card($offset, $match, $player)->attack();
    }

    public function sendToDefend(
        int $offset,
        MatchId $match,
        int $player
    ): void {
        $this->card($offset, $match, $player)->defend();
    }

    public function regroup(
        int $offset,
        MatchId $match,
        int $player
    ): void {
        $this->card($offset, $match, $player)->regroup();
    }

    /** @return Card[] */
    public function attackers(MatchId $match): array
    {
        return array_filter(
            $this->cardsInPlay($match),
            static function (Card $card): bool {
                return $card->isAttacking();
            }
        );
    }

    /** @return Card[] */
    public function defenders(MatchId $match): array
    {
        return array_filter(
            $this->cardsInPlay($match),
            static function (Card $card): bool {
                return $card->isDefending();
            }
        );
    }

    private function card(int $offset, MatchId $match, int $player): Card
    {
        return current(array_filter(
            $this->cardsInPlayFor($player, $match),
            static function (Card $card) use ($offset): bool {
                return $card->offset() === $offset;
            }
        ));
    }
}
