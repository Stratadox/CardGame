<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account;

interface AccountIdGenerator
{
    public function generate(): AccountId;
}
