<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use function array_filter;
use function array_reverse;
use function array_values;
use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Card\CardId;
use Stratadox\CardGame\Match\Card\Cards;
use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Player\PlayerId;

final class InMemoryCards implements Cards
{
    /** @var Card[] */
    private $cards = [];

    public function add(Card $card): void
    {
        $this->cards[(string) $card->id()] = $card;
    }

    public function inHandOf(PlayerId $thePlayer): array
    {
        return array_values(array_reverse(array_filter(
            $this->cards,
            function (Card $theCard) use ($thePlayer): bool {
                return $theCard->isInHandOf($thePlayer);
            }
        )));
    }

    public function topMostCardIn(DeckId $theDeck): Card
    {
        // @todo refactor to map reduction
        /** @var Card|null $topMostCard */
        $topMostCard = null;
        $highest = -1;
        foreach ($this->cardsThatAreIn($theDeck) as $theCard) {
            if ($theCard->position() > $highest) {
                $highest = $theCard->position();
                $topMostCard = $theCard;
            }
        }
        // @todo throw OutOfCards exception if topmost is null
        return $topMostCard;
    }

    public function withId(CardId $card): Card
    {
        return $this->cards[$card->id()];
    }

    /** @return Card[] */
    private function cardsThatAreIn(DeckId $theDeck): array
    {
        return array_filter($this->cards, function (Card $theCard) use ($theDeck): bool {
            return $theCard->isInDeck($theDeck);
        });
    }
}
