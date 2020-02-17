<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\ReadModel\Proposal\MatchProposals;
use function assert;
use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Proposal\ProposalWasAccepted;

final class ProposalAcceptanceNotifier implements EventHandler
{
    private $matchProposals;

    public function __construct(MatchProposals $matchProposals)
    {
        $this->matchProposals = $matchProposals;
    }

    public function events(): iterable
    {
        return [ProposalWasAccepted::class];
    }

    public function handle(DomainEvent $event): void
    {
        assert($event instanceof ProposalWasAccepted);

        $this->matchProposals->byId($event->aggregateId())->accept();
    }
}
