<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use function array_map;
use function array_values;
use Stratadox\CardGame\CardId;

final class Hand
{
    private $cards;

    public function __construct(Card ...$cards)
    {
        $this->cards = $cards;
    }

    public static function drawnFrom(Deck $deck, int $cards): self
    {
        $drawnCards = [];
        for ($i = $cards; $i > 0; $i--) {
            $drawnCards[] = $deck->draw();
        }
        return new self(...$drawnCards);
    }

    public function play(int $cardNumber): Card
    {
        $card = $this->cards[$cardNumber];
        unset($this->cards[$cardNumber]);
        $this->cards = array_values($this->cards);
        return $card;
    }

    /** @return CardId */
    public function cards(): array
    {
        return array_map(function (Card $card): CardId {
            return $card->id();
        }, $this->cards);
    }
}
