<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\ProposalId;

final class AcceptedProposal
{
    private $proposalId;
    private $acceptedSince;

    public function __construct(
        ProposalId $proposalId,
        DateTimeInterface $acceptedAt
    ) {
        $this->proposalId = $proposalId;
        $this->acceptedSince = $acceptedAt;
    }

    public function id(): ProposalId
    {
        return $this->proposalId;
    }

    public function acceptedSince(): DateTimeInterface
    {
        return $this->acceptedSince;
    }
}
