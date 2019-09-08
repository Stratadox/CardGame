<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use DateTimeInterface;
use Stratadox\CardGame\DomainEventRecorder;
use Stratadox\CardGame\DomainEventRecording;
use Stratadox\CardGame\Account\Event\VisitorOpenedAnAccount;
use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\Proposal\MatchProposal;
use Stratadox\CardGame\ProposalId;
use Stratadox\CardGame\VisitorId;

final class PlayerAccount implements DomainEventRecorder
{
    use DomainEventRecording;

    private $id;

    private function __construct(AccountId $id, AccountEvent $creationEvent)
    {
        $this->id = $id;
        $this->events = [$creationEvent];
    }

    public static function fromVisitor(VisitorId $visitor, AccountId $id): self
    {
        return new self($id, VisitorOpenedAnAccount::with($id, $visitor));
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function proposeMatchTo(
        AccountId $theOtherPlayer,
        DateTimeInterface $validUntil,
        ProposalId $proposalId
    ): MatchProposal {
        return new MatchProposal(
            $proposalId,
            $this->id,
            $theOtherPlayer,
            $validUntil
        );
    }
}
