<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_filter;
use function array_reduce;
use Closure;
use function count;
use Stratadox\ImmutableCollection\ImmutableCollection;

final class Cards extends ImmutableCollection
{
    public function __construct(Card ...$cards)
    {
        parent::__construct(...$cards);
    }

    public function current(): Card
    {
        return parent::current();
    }

    public function inHand(): Cards
    {
        return $this->filterBy(static function (Card $card): bool {
            return $card->isInHand();
        });
    }

    public function inPlay(): Cards
    {
        return $this->filterBy(static function (Card $card): bool {
            return $card->isInPlay();
        });
    }

    public function thatAttack(): Cards
    {
        return $this->filterBy(static function (Card $card): bool {
            return $card->isAttacking();
        });
    }

    public function thatDefend(): Cards
    {
        return $this->filterBy(static function (Card $card): bool {
            return $card->isDefending();
        });
    }

    private function inDeck(): Cards
    {
        return $this->filterBy(static function (Card $card): bool {
            return $card->isInDeck();
        });
    }

    public function drawFromTopOfDeck(MatchId $match, int $player): void
    {
        $this->inDeck()->topMost()->draw($match, count($this->inHand()), $player);
    }

    public function theOneThatAttacksTheAmbushOf(Card $defender): Card
    {
        return $this->filterBy(static function (Card $card) use ($defender): bool {
            return $card->isAttackingThe($defender);
        })[0];
    }

    private function topMost(): Card
    {
        return array_reduce(
            $this->items(),
            static function (?Card $topmost, Card $card): ?Card {
                if ($topmost === null || $card->hasHigherPositionThan($topmost)) {
                    return $card;
                }
                return $topmost;
            }
        );
    }

    private function filterBy(Closure $function): Cards
    {
        return new self(...array_filter($this->items(), $function));
    }
}
