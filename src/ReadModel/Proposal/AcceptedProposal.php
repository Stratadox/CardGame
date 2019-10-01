<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use Stratadox\CardGame\Proposal\ProposalId;

final class AcceptedProposal
{
    private $proposalId;

    public function __construct(ProposalId $proposalId)
    {
        $this->proposalId = $proposalId;
    }

    public function id(): ProposalId
    {
        return $this->proposalId;
    }
}
