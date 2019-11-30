<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Command;

use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;

final class CheckIfTurnPhaseExpired implements Command
{
    /** @var CorrelationId */
    private $correlationId;

    private function __construct(CorrelationId $correlationId)
    {
        $this->correlationId = $correlationId;
    }

    public static function with(CorrelationId $correlationId): self
    {
        return new self($correlationId);
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
