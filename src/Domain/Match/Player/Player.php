<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Match\Card\CardId;
use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Mana;

final class Player implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $mana;
    private $deck;

    private function __construct(PlayerId $id, Mana $mana, DeckId $deck)
    {
        $this->id = $id;
        $this->mana = $mana;
        $this->deck = $deck;
    }

    public static function with(PlayerId $id, DeckId $deck): self
    {
        return new self($id, new Mana(4), $deck);
    }

    public function id(): PlayerId
    {
        return $this->id;
    }

    public function deck(): DeckId
    {
        return $this->deck;
    }

    /** @throws CannotPlayThisCard */
    public function payFor(CardId $theCard, Mana $theCostOfTheCard): void
    {
        if ($this->mana->isLessThan($theCostOfTheCard)) {
            throw NotEnoughMana::toPlayTheCard();
        }
        $this->mana = $this->mana->minus($theCostOfTheCard);

        $this->events[] = new PaidForCard($this->id(), $theCard);
    }
}
