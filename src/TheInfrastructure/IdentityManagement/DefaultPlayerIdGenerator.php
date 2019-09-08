<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Match\Handler\PlayerIdGenerator;
use Stratadox\CardGame\PlayerId;

final class DefaultPlayerIdGenerator extends IdGenerator implements PlayerIdGenerator
{
    public function generate(): PlayerId
    {
        return PlayerId::from($this->newIdFor('player'));
    }
}
