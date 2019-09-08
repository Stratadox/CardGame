<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal\Handler;

use Stratadox\CardGame\ProposalId;

interface IdentityGenerator
{
    public function generate(): ProposalId;
}
