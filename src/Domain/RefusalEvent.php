<?php declare(strict_types=1);

namespace Stratadox\CardGame;

abstract class RefusalEvent implements DomainEvent
{
    private $correlationId;
    private $reason;

    public function __construct(CorrelationId $correlationId, string $reason)
    {
        $this->correlationId = $correlationId;
        $this->reason = $reason;
    }

    public function aggregateId(): CorrelationId
    {
        return $this->correlationId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
