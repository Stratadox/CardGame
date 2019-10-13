<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;

final class AcceptTheProposal implements Command
{
    private $proposalId;
    private $acceptingPlayer;
    private $correlationId;

    private function __construct(
        ProposalId $id,
        AccountId $acceptingPlayer,
        CorrelationId $correlationId
    ) {
        $this->proposalId = $id;
        $this->acceptingPlayer = $acceptingPlayer;
        $this->correlationId = $correlationId;
    }

    public static function withId(
        ProposalId $id,
        AccountId $acceptingPlayer,
        CorrelationId $correlationId
    ): self {
        return new self($id, $acceptingPlayer, $correlationId);
    }

    public function proposal(): ProposalId
    {
        return $this->proposalId;
    }

    public function acceptingPlayer(): AccountId
    {
        return $this->acceptingPlayer;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
