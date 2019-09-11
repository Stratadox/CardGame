<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\Visiting\VisitorId;

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
