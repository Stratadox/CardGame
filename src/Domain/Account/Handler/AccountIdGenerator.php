<?php declare(strict_types=1);

namespace Stratadox\CardGame\Account\Handler;

use Stratadox\CardGame\AccountId;

interface AccountIdGenerator
{
    public function generate(): AccountId;
}
