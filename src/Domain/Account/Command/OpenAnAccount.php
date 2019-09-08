<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account\Command;

use Stratadox\CardGame\VisitorId;

/**
 * @see \Stratadox\CardGame\Account\Handler\AccountOpeningProcess
 */
final class OpenAnAccount
{
    private $visitorId;

    private function __construct(VisitorId $visitorId)
    {
        $this->visitorId = $visitorId;
    }

    public static function forVisitorWith(VisitorId $visitorId): self
    {
        return new self($visitorId);
    }

    public function visitorId(): VisitorId
    {
        return $this->visitorId;
    }
}
