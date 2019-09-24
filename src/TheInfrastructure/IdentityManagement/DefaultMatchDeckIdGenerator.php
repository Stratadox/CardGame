<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\IdentityManagement;

use Stratadox\CardGame\Match\Deck\DeckId;
use Stratadox\CardGame\Match\Deck\DeckIdGenerator;

final class DefaultMatchDeckIdGenerator extends IdGenerator implements DeckIdGenerator
{
    public function generate(): DeckId
    {
        return DeckId::from($this->newIdFor('match'));
    }
}
