<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Account\AccountIdGenerator;
use Stratadox\CardGame\Account\AccountId;

final class DefaultAccountIdGenerator extends IdGenerator implements AccountIdGenerator
{
    public function generate(): AccountId
    {
        return AccountId::from($this->newIdFor('player'));
    }
}
