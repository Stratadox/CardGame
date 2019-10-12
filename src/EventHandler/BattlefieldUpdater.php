<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\UnitDied;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\Event\UnitMovedToAttack;
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

    public function events(): iterable
    {
        return [
            UnitMovedIntoPlay::class,
            UnitMovedToAttack::class,
            UnitDied::class
        ];
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UnitMovedIntoPlay) {
            $this->battlefield->add(
                $this->cards->ofType($event->card()),
                $event->aggregateId()
            );
        }
        if ($event instanceof UnitMovedToAttack) {
            // @todo wont work with doubles..
            $this->cards->ofType($event->card())->attack();
        }
        if ($event instanceof UnitDied) {
            $this->battlefield->remove(
                $this->cards->ofType($event->card()),
                $event->match()
            );
        }
    }
}
