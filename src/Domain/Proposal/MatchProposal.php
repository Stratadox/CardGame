<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\Proposal\Event\MatchWasProposed;
use Stratadox\CardGame\Proposal\Event\ProposalWasAccepted;
use Stratadox\CardGame\ProposalId;

final class MatchProposal implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $validUntil;
    private $acceptedAt;

    public function __construct(
        ProposalId $proposalId,
        AccountId $proposedBy,
        AccountId $proposedTo,
        DateTimeInterface $validUntil
    ) {
        $this->id = $proposalId;
        $this->validUntil = $validUntil;
        $this->events[] = new MatchWasProposed(
            $proposalId,
            $proposedBy,
            $proposedTo,
            $validUntil
        );
    }

    public function id(): ProposalId
    {
        return $this->id;
    }

    public function accept(DateTimeInterface $now): void
    {
        if ($now > $this->validUntil) {
            // @todo events[] = new TriedToAcceptExpiredProposal($this->id, $now)
            return;
        }
        $this->acceptedAt = $now;
        $this->events[] = new ProposalWasAccepted($this->id, $now);
    }
}
