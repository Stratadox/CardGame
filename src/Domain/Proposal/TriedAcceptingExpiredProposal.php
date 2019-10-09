<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\RefusalEvent;

final class TriedAcceptingExpiredProposal implements RefusalEvent
{
    private $proposal;

    public function __construct(CorrelationId $proposal)
    {
        $this->proposal = $proposal;
    }

    public function aggregateId(): CorrelationId
    {
        return $this->proposal;
    }
}
