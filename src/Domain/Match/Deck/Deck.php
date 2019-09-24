<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use Stratadox\CardGame\Match\Card\Card;
use Stratadox\CardGame\Match\Player\PlayerId;

final class Deck
{
    private $id;
    private $cards;
    private $owner;

    public function __construct(DeckId $id, PlayerId $owner, Card ...$cards)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->cards = $cards;
    }

    public function id(): DeckId
    {
        return $this->id;
    }

    public function owner(): PlayerId
    {
        return $this->owner;
    }

    // @todo shuffle

    /** @throws CardNotInDeck */
    public function cardAt(int $position): Card
    {
        if (!isset($this->cards[$position])) {
            throw CardNotInDeck::deckHasNoCardAt($position);
        }
        return $this->cards[$position];
    }
}
