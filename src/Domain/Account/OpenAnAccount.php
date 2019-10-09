<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Visiting\VisitorId;

final class OpenAnAccount
{
    private $visitorId;
    private $correlationId;

    private function __construct(VisitorId $visitorId, CorrelationId $correlationId)
    {
        $this->visitorId = $visitorId;
        $this->correlationId = $correlationId;
    }

    public static function forVisitorWith(
        VisitorId $visitorId,
        CorrelationId $correlationId
    ): self {
        return new self($visitorId, $correlationId);
    }

    public function visitorId(): VisitorId
    {
        return $this->visitorId;
    }

    public function correlationId(): CorrelationId
    {
        return $this->correlationId;
    }
}
