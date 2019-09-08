<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal\Event;

use DateTimeInterface;
use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\Proposal\ProposalEvent;
use Stratadox\CardGame\ProposalId;

final class MatchWasProposed implements ProposalEvent
{
    private $id;
    private $proposedBy;
    private $proposedTo;
    private $validUntil;

    public function __construct(
        ProposalId $proposalId,
        AccountId $proposedBy,
        AccountId $proposedTo,
        DateTimeInterface $validUntil
    ) {
        $this->id = $proposalId;
        $this->proposedBy = $proposedBy;
        $this->proposedTo = $proposedTo;
        $this->validUntil = $validUntil;
    }

    public function aggregateId(): ProposalId
    {
        return $this->id;
    }

    public function proposedBy(): AccountId
    {
        return $this->proposedBy;
    }

    public function proposedTo(): AccountId
    {
        return $this->proposedTo;
    }

    public function validUntil(): DateTimeInterface
    {
        return $this->validUntil;
    }

    public function payload(): array
    {
        return [
            'proposedBy' => $this->proposedBy(),
            'proposedTo' => $this->proposedTo(),
            'validUntil' => $this->validUntil(),
        ];
    }
}
