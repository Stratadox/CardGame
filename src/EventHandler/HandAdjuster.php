<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\CardWasDrawn;
use Stratadox\CardGame\Match\Event\SpellVanishedToTheVoid;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardTemplates;
use Stratadox\CardGame\ReadModel\Match\CardsInHand;

final class HandAdjuster implements EventHandler
{
    private $cardsInHand;
    private $cardTemplates;

    public function __construct(
        CardsInHand $cardsInHand,
        CardTemplates $cardTemplates
    ) {
        $this->cardsInHand = $cardsInHand;
        $this->cardTemplates = $cardTemplates;
    }

    public function events(): iterable
    {
        return [
            UnitMovedIntoPlay::class,
            SpellVanishedToTheVoid::class,
            CardWasDrawn::class,
        ];
    }

    public function handle(DomainEvent $event): void
    {
        if (
            $event instanceof UnitMovedIntoPlay ||
            $event instanceof SpellVanishedToTheVoid
        ) {
            $this->cardsInHand->played(
                $event->offset(),
                $event->match(),
                $event->player()
            );
        } else if ($event instanceof CardWasDrawn) {
            $this->cardsInHand->draw(
                $event->match(),
                $event->player(),
                new Card(
                    $event->offset(),
                    $this->cardTemplates->ofType($event->card())
                )
            );
        }
    }
}
