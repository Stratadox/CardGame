<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CardId;
use Stratadox\CardGame\PlayerId;

final class Player
{
    private $id;
    private $deck;
    private $hand;
    private $mana;
    private $playedCards = [];

    private function __construct(PlayerId $id, Deck $deck, Hand $hand, Mana $mana)
    {
        $this->id = $id;
        $this->deck = $deck;
        $this->hand = $hand;
        $this->mana = $mana;
    }

    public static function with(PlayerId $id, Deck $deck): self
    {
        return new self($id, $deck, Hand::drawnFrom($deck, 7), new Mana(4));
    }

    public function id(): PlayerId
    {
        return $this->id;
    }

    /** @throws CannotPlayThisCard */
    public function playOn(Battlefield $theBattlefield, int $cardNumber): void
    {
        if ($this->mana->isLessThan($this->hand->costOf($cardNumber))) {
            throw NotEnoughMana::toPlay($this->card($cardNumber));
        }
        $this->mana = $this->mana->minus($this->hand->costOf($cardNumber));
        $card = $this->hand->take($cardNumber);
        $card->putIntoActionOn($theBattlefield);
        $this->playedCards[] = $card;
    }

    /** @return Card[] */
    public function takePlayedCards(): array
    {
        $cards = $this->playedCards;
        $this->playedCards = [];
        return $cards;
    }

    /** @return CardId[] */
    public function cardsInHand(): iterable
    {
        return $this->hand->cards();
    }

    private function card(int $number): CardId
    {
        return $this->cardsInHand()[$number];
    }
}
