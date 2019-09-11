<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

interface ProposedMatches
{
    public function add(MatchProposal $proposal): void;
    public function withId(ProposalId $id): ?MatchProposal;
}
