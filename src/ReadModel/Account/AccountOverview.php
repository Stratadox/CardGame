<?php declare(strict_types=1);

namespace Stratadox\CardGame\ReadModel\Account;

use Stratadox\CardGame\AccountId;
use Stratadox\CardGame\VisitorId;

final class AccountOverview
{
    private $id;
    private $visitorId;

    public function __construct(AccountId $id, VisitorId $visitorId)
    {
        $this->id = $id;
        $this->visitorId = $visitorId;
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function visitor(): VisitorId
    {
        return $this->visitorId;
    }

    public function isGuestAccount(): bool
    {
        return true;
    }
}
