<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Proposal\ProposalIdGenerator;
use Stratadox\CardGame\Proposal\ProposalId;

final class DefaultProposalIdGenerator extends IdGenerator implements ProposalIdGenerator
{
    public function generate(): ProposalId
    {
        return ProposalId::from($this->newIdFor('proposal'));
    }
}
