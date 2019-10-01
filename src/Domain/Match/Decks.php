<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

use Stratadox\ImmutableCollection\ImmutableCollection;

final class Decks extends ImmutableCollection
{
    public function __construct(Deck ...$decks)
    {
        parent::__construct(...$decks);
    }

    public function offsetGet($position): Deck
    {
        return parent::offsetGet($position);
    }
}
