<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use Stratadox\CardGame\DomainEvent;

interface ProposalEvent extends DomainEvent
{
    public function aggregateId(): ProposalId;
}
