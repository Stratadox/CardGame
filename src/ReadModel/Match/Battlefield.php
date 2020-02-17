<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_filter;
use function array_merge;
use function current;

class Battlefield
{
    private $cards;

    public static function untouched(): self
    {
        return new self();
    }

    public function addFor(int $owner, Card $card): void
    {
        $this->cards[$owner][] = $card;
    }

    public function removeFrom(int $owner, int $cardToRemove): void
    {
        $this->cards[$owner] = array_filter(
            $this->cards[$owner],
            static function (Card $card) use ($cardToRemove): bool {
                return $card->offset() !== $cardToRemove;
            }
        );
    }

    /** @return Card[] */
    public function cardsInPlay(): array
    {
        // @todo this fails if all cards die: by then cards isn't null but []
        return array_merge(...$this->cards ?? [[]]);
    }

    /** @return Card[] */
    public function cardsInPlayFor(int $player): array
    {
        return $this->cards[$player];
    }

    public function getSentIntoBattleBy(int $player, int $attacker): void
    {
        $this->card($attacker, $player)->attack();
    }

    public function getSentToDefendBy(int $player, int $defender): void
    {
        $this->card($defender, $player)->defend();
    }

    public function getSentToRegroupBy(int $player, int $veteran): void
    {
        $this->card($veteran, $player)->regroup();
    }

    /** @return Card[] */
    public function attackers(): array
    {
        return array_filter(
            $this->cardsInPlay(),
            static function (Card $card): bool {
                return $card->isAttacking();
            }
        );
    }

    /** @return Card[] */
    public function defenders(): array
    {
        return array_filter(
            $this->cardsInPlay(),
            static function (Card $card): bool {
                return $card->isDefending();
            }
        );
    }

    private function card(int $offset, int $player): Card
    {
        return current(array_filter(
            $this->cardsInPlayFor($player),
            static function (Card $card) use ($offset): bool {
                return $card->offset() === $offset;
            }
        ));
    }
}
