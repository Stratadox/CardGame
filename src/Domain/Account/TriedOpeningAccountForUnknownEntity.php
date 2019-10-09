<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\RefusalEvent;

final class TriedOpeningAccountForUnknownEntity implements RefusalEvent
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
