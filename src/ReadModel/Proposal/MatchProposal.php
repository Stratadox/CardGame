<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\MatchId;
use Stratadox\CardGame\Proposal\ProposalId;

final class MatchProposal
{
    /** @var ProposalId */
    private $id;
    /** @var AccountId */
    private $from;
    /** @var AccountId */
    private $to;
    /** @var DateTimeInterface */
    private $validUntil;
    /** @var bool */
    private $accepted = false;
    /** @var null|MatchId */
    private $match;

    public function __construct(
        ProposalId $id,
        AccountId $from,
        AccountId $to,
        DateTimeInterface $validUntil
    ) {
        $this->id = $id;
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
        return !$this->accepted
            && $this->to->is($player)
            && $when <= $this->validUntil;
    }

    public function wasProposedBy(AccountId $account): bool
    {
        return $this->from->is($account);
    }

    public function wasProposedTo(AccountId $account): bool
    {
        return $this->to->is($account);
    }

    public function hasBeenAccepted(): bool
    {
        return $this->accepted;
    }

    public function accept(): void
    {
        $this->accepted = true;
    }

    public function begin(MatchId $match): void
    {
        $this->match = $match;
    }
}
