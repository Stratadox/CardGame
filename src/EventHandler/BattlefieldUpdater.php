<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\UnitDied;
use Stratadox\CardGame\Match\Event\UnitMovedIntoPlay;
use Stratadox\CardGame\Match\Event\UnitMovedToAttack;
use Stratadox\CardGame\Match\Event\UnitMovedToDefend;
use Stratadox\CardGame\Match\Event\UnitRegrouped;
use Stratadox\CardGame\Match\MatchEvent;
use Stratadox\CardGame\ReadModel\Match\Battlefield;
use Stratadox\CardGame\ReadModel\Match\Card;
use Stratadox\CardGame\ReadModel\Match\CardTemplates;
use Stratadox\CardGame\ReadModel\Match\Battlefields;
use function assert;

final class BattlefieldUpdater implements EventHandler
{
    private $battlefield;
    private $cardTemplate;

    public function __construct(
        Battlefields $battlefield,
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
            UnitMovedToDefend::class,
            UnitRegrouped::class,
            UnitDied::class,
        ];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof MatchEvent);
        $this->update($this->battlefield->for($event->aggregateId()), $event);
    }

    private function update(Battlefield $battlefield, MatchEvent $event): void
    {
        if ($event instanceof UnitMovedIntoPlay) {
            $battlefield->addFor($event->player(), new Card(
                $event->offset(),
                $this->cardTemplate->ofType($event->card())
            ));
        } elseif ($event instanceof UnitMovedToAttack) {
            $battlefield->getSentIntoBattleBy($event->player(), $event->offset());
        } elseif ($event instanceof UnitMovedToDefend) {
            $battlefield->getSentToDefendBy($event->player(), $event->offset());
        } elseif ($event instanceof UnitRegrouped) {
            $battlefield->getSentToRegroupBy($event->player(), $event->offset());
        } elseif ($event instanceof UnitDied) {
            $battlefield->removeFrom($event->player(), $event->offset());
        }
    }
}
