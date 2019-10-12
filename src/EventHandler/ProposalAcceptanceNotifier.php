<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Proposal\ProposalWasAccepted;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposal;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;

final class ProposalAcceptanceNotifier implements EventHandler
{
    private $acceptedProposals;

    public function __construct(AcceptedProposals $acceptedProposals)
    {
        $this->acceptedProposals = $acceptedProposals;
    }

    public function events(): iterable
    {
        return [ProposalWasAccepted::class];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof ProposalWasAccepted);

        $this->acceptedProposals->add(
            new AcceptedProposal($event->aggregateId())
        );
    }
}
