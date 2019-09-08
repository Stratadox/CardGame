<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal\Event;

use DateTimeInterface;
use Stratadox\CardGame\Proposal\ProposalEvent;
use Stratadox\CardGame\ProposalId;

final class ProposalWasAccepted implements ProposalEvent
{
    private $proposal;
    private $when;

    public function __construct(ProposalId $proposal, DateTimeInterface $when)
    {
        $this->proposal = $proposal;
        $this->when = $when;
    }

    public function aggregateId(): ProposalId
    {
        return $this->proposal;
    }

    public function when(): DateTimeInterface
    {
        return $this->when;
    }

    public function payload(): array
    {
        return [
            'when' => $this->when(),
        ];
    }
}
