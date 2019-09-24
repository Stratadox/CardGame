<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\Match\Match\Match;
use Stratadox\CardGame\Match\Match\MatchId;
use Stratadox\CardGame\Match\Player\PlayerId;
use Stratadox\CardGame\Match\Match\StartedSettingUpMatchForProposal;

final class MatchProposal implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;
    private $validUntil;
    private $acceptedAt;
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

    public function accept(DateTimeInterface $now): void
    {
        if ($now > $this->validUntil) {
            // @todo events[] = new TriedToAcceptExpiredProposal($this->id, $now)
            return;
        }
        $this->acceptedAt = $now;
        $this->events[] = new ProposalWasAccepted($this->id, $now);
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
    public function prepare(MatchId $theMatch, PlayerId ...$players): Match
    {
        if (!$this->acceptedAt) {
            throw ProposalHasNotBeenAccepted::cannotStartMatch($this->id);
        }
        return Match::prepare(
            $theMatch,
            [new StartedSettingUpMatchForProposal($theMatch, $this->id, ...$players)],
            ...$players
        );
    }
}
