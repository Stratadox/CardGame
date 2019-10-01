<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

final class ProposalWasAccepted implements ProposalEvent
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
