<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CardId;
use Stratadox\CardGame\PlayerId;

final class Player
{
    private $id;
    private $deck;
    private $hand;

    public function __construct(PlayerId $id, Deck $deck, Hand $hand)
    {
        $this->id = $id;
        $this->deck = $deck;
        $this->hand = $hand;
    }

    public static function with(PlayerId $id, Deck $deck): self
    {
        return new self($id, $deck, Hand::drawnFrom($deck, 7));
    }

    public function id(): PlayerId
    {
        return $this->id;
    }

    public function play(int $cardNumber): Card
    {
        return $this->hand->play($cardNumber);
    }

    /** @return CardId[] */
    public function cardsInHand(): iterable
    {
        return $this->hand->cards();
    }
}
