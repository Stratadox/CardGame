<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\CardWasDrawn;
use Stratadox\CardGame\Match\SpellVanishedToTheVoid;
use Stratadox\CardGame\Match\UnitMovedIntoPlay;
use Stratadox\CardGame\ReadModel\Match\AllCards;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;

final class HandAdjuster implements EventHandler
{
    private $cardsInHand;
    private $cards;

    public function __construct(CardsInHand $cardsInHand, AllCards $allCards)
    {
        $this->cardsInHand = $cardsInHand;
        $this->cards = $allCards;
    }

    public function handle(DomainEvent $event): void
    {
        if(
            $event instanceof UnitMovedIntoPlay ||
            $event instanceof SpellVanishedToTheVoid
        ) {
            $this->cardsInHand->played(
                (string) $event->card(),
                $event->match(),
                $event->player()
            );
        } else if ($event instanceof CardWasDrawn) {
            $this->cardsInHand->draw(
                $event->match(),
                $event->player(),
                $this->cards->withId($event->card())
            );
        }
    }
}
