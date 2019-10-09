<?php declare(strict_types=1);

namespace Stratadox\CardGame\Proposal;

use Stratadox\CardGame\Account\AccountId;
use Stratadox\CardGame\CorrelationId;

final class ProposeMatch
{
    private $proposedBy;
    private $proposedTo;
    private $correlationId;

    private function __construct(
        AccountId $proposedBy,
        AccountId $proposedTo,
        CorrelationId $correlationId
    ) {
        $this->proposedBy = $proposedBy;
        $this->proposedTo = $proposedTo;
        $this->correlationId = $correlationId;
    }

    public static function between(
        AccountId $proposedBy,
        AccountId $proposedTo,
        CorrelationId $correlationId
    ): ProposeMatch {
        return new self($proposedBy, $proposedTo, $correlationId);
    }

    public function proposedBy(): AccountId
    {
        return $this->proposedBy;
    }

    public function proposedTo(): AccountId
    {
        return $this->proposedTo;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
