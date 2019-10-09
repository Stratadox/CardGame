<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Proposal\ProposalId;

final class StartTheMatch
{
    private $proposal;
    private $correlationId;

    public function __construct(ProposalId $proposal, CorrelationId $correlationId)
    {
        $this->proposal = $proposal;
        $this->correlationId = $correlationId;
    }

    public static function forProposal(
        ProposalId $proposal,
        CorrelationId $correlationId
    ): self {
        return new self($proposal, $correlationId);
    }

    public function proposal(): ProposalId
    {
        return $this->proposal;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
