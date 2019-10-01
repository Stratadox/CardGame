<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_filter;
use function array_reduce;
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
        return new self(...array_filter(
            $this->items(),
            function (Card $card): bool {
                return $card->isInHand();
            }
        ));
    }

    public function inPlay(): Cards
    {
        return new self(...array_filter(
            $this->items(),
            function (Card $card): bool {
                return $card->isInPlay();
            }
        ));
    }

    public function inDeck(): Cards
    {
        return new self(...array_filter(
            $this->items(),
            function (Card $card): bool {
                return $card->isInDeck();
            }
        ));
    }

    public function drawFromTopOfDeck(MatchId $match, PlayerId $player): void
    {
        $this->inDeck()->topMost()->draw($match, count($this->inHand()), $player);
    }

    private function topMost(): Card
    {
        return array_reduce(
            $this->items(),
            function (?Card $topmost, Card $card): ?Card {
                if ($topmost === null || $card->hasHigherPositionThan($topmost)) {
                    return $card;
                }
                return $topmost;
            }
        );
    }
}
