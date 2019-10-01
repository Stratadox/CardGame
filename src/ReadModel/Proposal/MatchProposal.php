<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Proposal\ProposalId;

final class MatchProposal
{
    private $id;
    private $from;
    private $to;
    private $validUntil;

    public function __construct(
        ProposalId $proposalId,
        AccountId $from,
        AccountId $to,
        DateTimeInterface $validUntil
    ) {
        $this->id = $proposalId;
        $this->from = $from;
        $this->to = $to;
        $this->validUntil = $validUntil;
    }

    public function id(): ProposalId
    {
        return $this->id;
    }

    public function canBeAcceptedBy(AccountId $player, DateTimeInterface $when): bool
    {
        return $this->to->is($player) && $when <= $this->validUntil;
    }
}
