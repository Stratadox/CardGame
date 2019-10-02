<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\DomainEvent;
use Stratadox\CardGame\Proposal\ProposalId;

final class TriedStartingMatchForPendingProposal implements DomainEvent
{
    private $proposal;

    public function __construct(ProposalId $proposal)
    {
        $this->proposal = $proposal;
    }

    public function aggregateId(): ProposalId
    {
        return $this->proposal;
    }
}
