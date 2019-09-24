<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Match\Match\MatchIdGenerator;
use Stratadox\CardGame\Match\Match\MatchId;

final class DefaultMatchIdGenerator extends IdGenerator implements MatchIdGenerator
{
    public function generate(): MatchId
    {
        return MatchId::from($this->newIdFor('match'));
    }
}
