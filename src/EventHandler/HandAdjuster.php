<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function array_map;
use function assert;
use Stratadox\CardGame\CardId;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\CardWasPlayed;
use Stratadox\CardGame\Match\Event\PlayerDrewOpeningHand;
use Stratadox\CardGame\ReadModel\Match\AllCards;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;

final class HandAdjuster implements EventHandler
{
    private $cardsInHand;
    private $allCards;

    public function __construct(CardsInHand $cardsInHand, AllCards $allCards)
    {
        $this->cardsInHand = $cardsInHand;
        $this->allCards = $allCards;
    }

    public function handle(DomainEvent $event): void
    {
        if($event instanceof CardWasPlayed) {
            $this->cardsInHand->played($event->card(), $event->player());
        } else {
            assert($event instanceof PlayerDrewOpeningHand);
            $this->cardsInHand->draw($event->player(), ...array_map(
                function (CardId $id) : Card {
                    return $this->allCards->withId($id);
                },
                $event->cards()
            ));
        }
    }
}
