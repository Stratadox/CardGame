<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Proposal\MatchWasProposed;
use Stratadox\CardGame\ReadModel\Linker;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposal;
use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;

final class ProposalSender implements EventHandler
{
    private $proposals;

    public function __construct(MatchProposals $proposals)
    {
        $this->proposals = $proposals;
    }

    public function events(): iterable
    {
        return [MatchWasProposed::class];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof MatchWasProposed);

        $this->proposals->add(
            new MatchProposal(
                $event->aggregateId(),
                $event->proposedBy(),
                $event->proposedTo(),
                $event->validUntil()
            )
        );
    }
}
