<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;

class AcceptedProposals
{
    private $proposals = [];

    public function add(AcceptedProposal $proposal): void
    {
        $this->proposals[] = $proposal;
    }

    /** @return AcceptedProposal[] */
    public function since(DateTimeInterface $begin): array
    {
        return $this->proposals;
    }
}
