<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\UnitMovedToAttack;
use Stratadox\CardGame\ReadModel\Match\AllCards;
use Stratadox\CardGame\ReadModel\Match\Battlefield;

final class BattlefieldUpdater implements EventHandler
{
    private $battlefield;
    private $cards;

    public function __construct(Battlefield $battlefield, AllCards $allCards)
    {
        $this->battlefield = $battlefield;
        $this->cards = $allCards;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UnitMovedIntoPlay) {
            $this->battlefield->add(
                $this->cards->withId($event->card()),
                $event->aggregateId()
            );
        }
        if ($event instanceof UnitMovedToAttack) {
            $this->cards->withId($event->card())->attack();
        }
    }
}
