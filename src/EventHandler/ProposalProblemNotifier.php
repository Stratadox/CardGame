<?php declare(strict_types=1);

namespace Stratadox\CardGame\EventHandler;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Match\TriedStartingMatchForPendingProposal;
use Stratadox\CardGame\ReadModel\ProposalProblemStream;

final class ProposalProblemNotifier implements EventHandler
{
    private $proposalProblems;

    public function __construct(ProposalProblemStream $proposalProblems)
    {
        $this->proposalProblems = $proposalProblems;
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof TriedStartingMatchForPendingProposal) {
            $this->proposalProblems->addFor(
                $event->aggregateId(),
                'The proposal is still pending!'
            );
        }
    }
}
