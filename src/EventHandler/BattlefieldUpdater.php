<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\UnitDied;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\Event\UnitMovedToAttack;
use Stratadox\CardGame\Match\Event\UnitRegrouped;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardTemplates;
use Stratadox\CardGame\ReadModel\Match\Battlefield;

final class BattlefieldUpdater implements EventHandler
{
    private $battlefield;
    private $cardTemplate;

    public function __construct(
        Battlefield $battlefield,
        CardTemplates $templates
    ) {
        $this->battlefield = $battlefield;
        $this->cardTemplate = $templates;
    }

    public function events(): iterable
    {
        return [
            UnitMovedIntoPlay::class,
            UnitMovedToAttack::class,
            UnitRegrouped::class,
            UnitDied::class,
        ];
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UnitMovedIntoPlay) {
            $this->battlefield->add(
                new Card(
                    $event->offset(),
                    $this->cardTemplate->ofType($event->card())
                ),
                $event->match(),
                $event->player()
            );
        }
        if ($event instanceof UnitMovedToAttack) {
            $this->battlefield->sendIntoBattle(
                $event->offset(),
                $event->match(),
                $event->player()
            );
        }
        if ($event instanceof UnitRegrouped) {
            $this->battlefield->regroup(
                $event->offset(),
                $event->match(),
                $event->player()
            );
        }
        if ($event instanceof UnitDied) {
            $this->battlefield->remove(
                $event->offset(),
                $event->match(),
                $event->player()
            );
        }
    }
}
