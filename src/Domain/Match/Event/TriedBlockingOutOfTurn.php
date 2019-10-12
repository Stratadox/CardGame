<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Event;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\RefusalEvent;

final class TriedBlockingOutOfTurn implements RefusalEvent
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
