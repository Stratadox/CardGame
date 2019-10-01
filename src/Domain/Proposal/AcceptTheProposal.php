<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use Stratadox\CardGame\Account\AccountId;

final class AcceptTheProposal
{
    private $proposalId;
    private $acceptingPlayer;

    private function __construct(ProposalId $id, AccountId $acceptingPlayer)
    {
        $this->proposalId = $id;
        $this->acceptingPlayer = $acceptingPlayer;
    }

    public static function withId(ProposalId $id, AccountId $acceptingPlayer): self
    {
        return new self($id, $acceptingPlayer);
    }

    public function proposal(): ProposalId
    {
        return $this->proposalId;
    }

    public function acceptingPlayer(): AccountId
    {
        return $this->acceptingPlayer;
    }
}
