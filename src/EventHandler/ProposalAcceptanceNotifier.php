<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Proposal\ProposalWasAccepted;
use Stratadox\CardGame\ReadModel\Proposal\AcceptedProposals;

final class ProposalAcceptanceNotifier implements EventHandler
{
    private $acceptedProposals;
    private $matchProposals;

    public function __construct(
        AcceptedProposals $acceptedProposals,
        MatchProposals $matchProposals
    ) {
        $this->acceptedProposals = $acceptedProposals;
        $this->matchProposals = $matchProposals;
    }

    public function events(): iterable
    {
        return [ProposalWasAccepted::class];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof ProposalWasAccepted);

        $this->acceptedProposals->add(
            $this->matchProposals->byId($event->aggregateId())
        );
    }
}
