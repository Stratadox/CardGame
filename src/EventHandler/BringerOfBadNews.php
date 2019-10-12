<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\Account\TriedOpeningAccountForUnknownEntity;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\Event\TriedPlayingCardOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingExpiredProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingUnknownProposal;
use Stratadox\CardGame\ReadModel\Refusals;

final class BringerOfBadNews implements EventHandler
{
    private $refusals;

    public function __construct(Refusals $refusals)
    {
        $this->refusals = $refusals;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof TriedOpeningAccountForUnknownEntity) {
            $this->refusals->addFor(
                $event->aggregateId(),
                'Cannot open account for unknown entity'
            );
        }
        if ($event instanceof TriedStartingMatchForPendingProposal) {
            $this->refusals->addFor(
                $event->aggregateId(),
                'The proposal is still pending!'
            );
        }
        if ($event instanceof TriedAcceptingExpiredProposal) {
            $this->refusals->addFor(
                $event->aggregateId(),
                'The proposal has already expired!'
            );
        }
        if ($event instanceof TriedAcceptingUnknownProposal) {
            $this->refusals->addFor(
                $event->aggregateId(),
                'Proposal not found'
            );
        }
        if ($event instanceof PlayerDidNotHaveTheMana) {
            $this->refusals->addFor($event->aggregateId(), 'Not enough mana!');
        }
        if ($event instanceof TriedPlayingCardOutOfTurn) {
            $this->refusals->addFor($event->aggregateId(), 'Cannot play cards right now');
        }
    }
}
