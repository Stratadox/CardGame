<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Deck;

interface DeckShuffler
{
    public function shuffle(Deck $theDeck): void;
}
