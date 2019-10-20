<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\Account\TriedOpeningAccountForUnknownEntity;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\Event\PlayerDidNotHaveTheMana;
use Stratadox\CardGame\Match\Event\TriedAttackingOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedAttackingWithUnknownCard;
use Stratadox\CardGame\Match\Event\TriedBlockingOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedBlockingWithUnknownCard;
use Stratadox\CardGame\Match\Event\TriedEndingPlayPhaseOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedPlayingCardOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedStartingCombatOutOfTurn;
use Stratadox\CardGame\Match\Event\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\Match\Event\TriedStartingMatchWithoutProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingExpiredProposal;
use Stratadox\CardGame\Proposal\TriedAcceptingUnknownProposal;
use Stratadox\CardGame\ReadModel\Refusals;
use Stratadox\CardGame\RefusalEvent;

final class BringerOfBadNews implements EventHandler
{
    private $refusals;

    public function __construct(Refusals $refusals)
    {
        $this->refusals = $refusals;
    }

    public function events(): iterable
    {
        return [
            TriedOpeningAccountForUnknownEntity::class,
            TriedStartingMatchForPendingProposal::class,
            TriedAcceptingExpiredProposal::class,
            TriedAcceptingUnknownProposal::class,
            TriedStartingMatchWithoutProposal::class,
            PlayerDidNotHaveTheMana::class,
            TriedPlayingCardOutOfTurn::class,
            TriedEndingPlayPhaseOutOfTurn::class,
            TriedAttackingWithUnknownCard::class,
            TriedAttackingOutOfTurn::class,
            TriedBlockingWithUnknownCard::class,
            TriedBlockingOutOfTurn::class,
            TriedStartingCombatOutOfTurn::class,
        ];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof RefusalEvent);

        $this->refusals->addFor($event->aggregateId(), $event->reason());
    }
}
