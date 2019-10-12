<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\RefusalEvent;

final class TriedPlayingCardOutOfTurn implements RefusalEvent
{
    private $correlationId;

    public function __construct(CorrelationId $correlationId)
    {
        $this->correlationId = $correlationId;
    }

    public function aggregateId(): CorrelationId
    {
        return $this->correlationId;
    }
}
