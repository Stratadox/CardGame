<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Proposal\Handler\IdentityGenerator;
use Stratadox\CardGame\ProposalId;

final class DefaultProposalIdGenerator extends IdGenerator implements IdentityGenerator
{
    public function generate(): ProposalId
    {
        return ProposalId::from($this->newIdFor('proposal'));
    }
}
