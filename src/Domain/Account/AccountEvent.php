<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

use Stratadox\CardGame\DomainEvent;

interface AccountEvent extends DomainEvent
{
    public function aggregateId(): AccountId;
}
