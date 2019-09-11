<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

interface ProposalIdGenerator
{
    public function generate(): ProposalId;
}
