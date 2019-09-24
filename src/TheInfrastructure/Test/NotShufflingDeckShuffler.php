<?php declare(strict_types=1);

namespace Stratadox\CardGame\Infrastructure\Test;

use Stratadox\CardGame\EventBag;
use Stratadox\CardGame\Match\Deck\Deck;
use Stratadox\CardGame\Match\Deck\DeckHasBeenShuffled;
use Stratadox\CardGame\Match\Deck\DeckShuffler;

final class NotShufflingDeckShuffler implements DeckShuffler
{
    private $eventBag;

    public function __construct(EventBag $eventBag)
    {
        $this->eventBag = $eventBag;
    }

    public function shuffle(Deck $theDeck): void
    {
        // We're truly cheating here: acting as though the deck had been shuffled
        // without actually shuffling it.
        $this->eventBag->add(new DeckHasBeenShuffled($theDeck->id()));
    }
}
