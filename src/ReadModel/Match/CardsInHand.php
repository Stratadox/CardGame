<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Match;

use function array_merge as combine_cards;
use function array_values;
use Stratadox\CardGame\CardId;
use Stratadox\CardGame\PlayerId;

class CardsInHand
{
    /** @var Card[][] */
    private $cards = [];

    public function draw(PlayerId $player, Card ...$cards): void
    {
        $this->cards[$player->id()] = combine_cards($this->of($player), $cards);
    }

    public function played(CardId $card, PlayerId $player): void
    {
        foreach ($this->cards[$player->id()] as $cardNumber => $cardInHand) {
            if ($card->is($cardInHand->id())) {
                unset($this->cards[$player->id()][$cardNumber]);
            }
        }
        $this->cards[$player->id()] = array_values($this->cards[$player->id()]);
    }

    /** @return Card[] */
    public function of(PlayerId $player): iterable
    {
        return $this->cards[$player->id()] ?? [];
    }
}
