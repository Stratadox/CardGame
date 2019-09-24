<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

use function assert;
use Stratadox\CommandHandling\Handler;

final class DeckShufflingProcess implements Handler
{
    private $decks;
    private $shuffler;

    public function __construct(Decks $decks, DeckShuffler $shuffler)
    {
        $this->decks = $decks;
        $this->shuffler = $shuffler;
    }

    public function handle(object $deckShuffle): void
    {
        assert($deckShuffle instanceof ShuffleDeck);

        $this->shuffler->shuffle($this->decks->forPlayer($deckShuffle->owner()));

        // @todo add to event bag
    }
}
