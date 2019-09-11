<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

final class AcceptTheProposal
{
    private $proposalId;

    private function __construct(ProposalId $id)
    {
        $this->proposalId = $id;
    }

    public static function withId(ProposalId $id): self
    {
        return new self($id);
    }

    public function proposalId(): ProposalId
    {
        return $this->proposalId;
    }
}
