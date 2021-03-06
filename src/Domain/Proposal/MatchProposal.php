<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\Decks;
use Stratadox\CardGame\Match\Match;
use Stratadox\CardGame\Match\MatchId;

final class MatchProposal implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $validUntil;
    private $isAccepted;
    private $proposedBy;
    private $proposedTo;

    public function __construct(
        ProposalId $proposalId,
        AccountId $proposedBy,
        AccountId $proposedTo,
        DateTimeInterface $validUntil
    ) {
        $this->id = $proposalId;
        $this->validUntil = $validUntil;
        $this->proposedTo = $proposedTo;
        $this->proposedBy = $proposedBy;
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

    /** @throws ProposalHasAlreadyExpired */
    public function accept(DateTimeInterface $now): void
    {
        if ($now > $this->validUntil) {
            throw ProposalHasAlreadyExpired::cannotAcceptItAnymore();
        }
        $this->isAccepted = true;
        $this->events[] = new ProposalWasAccepted($this->id);
    }

    public function proposedBy(): AccountId
    {
        return $this->proposedBy;
    }

    public function proposedTo(): AccountId
    {
        return $this->proposedTo;
    }

    /** @throws ProposalHasNotBeenAccepted */
    public function start(
        MatchId $match,
        Decks $decks,
        DateTimeInterface $when
    ): Match {
        if (!$this->isAccepted) {
            throw ProposalHasNotBeenAccepted::cannotStartMatch();
        }
        return Match::fromProposal($match, $this->id, $decks, $when);
    }
}
