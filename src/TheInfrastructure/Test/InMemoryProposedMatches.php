<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\Proposal\MatchProposal;
use Stratadox\CardGame\Proposal\ProposedMatches;
use Stratadox\CardGame\Proposal\ProposalId;

final class InMemoryProposedMatches implements ProposedMatches
{
    /** @var MatchProposal[] */
    private $proposals = [];

    public function add(MatchProposal $proposal): void
    {
        $this->proposals[(string) $proposal->id()] = $proposal;
    }

    public function withId(ProposalId $id): ?MatchProposal
    {
        return $this->proposals[(string) $id] ?? null;
    }
}
