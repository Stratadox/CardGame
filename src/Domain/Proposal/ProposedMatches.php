<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use DateTimeInterface as Moment;
use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\ProposalId;

interface ProposedMatches
{
    public function add(MatchProposal $proposal): void;
    public function withId(ProposalId $id): ?MatchProposal;
}
